# SGE – Sistema de Gestão de Estoque

Sistema web para controlo de entradas, saídas e stock em tempo real, desenvolvido para uma empresa em Moçambique.

## Visão Geral

O SGE substitui o controlo de inventário por Excel, oferecendo:

- **Registo de faturas** de compra com múltiplos itens
- **Requisições** de materiais por funcionários/motoristas
- **Stock calculado em tempo real** (entradas via faturas − saídas via requisições)
- **Dashboard** com alertas de stock baixo e últimos movimentos
- **Relatórios** de stock actual, movimentos e utilização

---

## Stack Tecnológica

| Camada | Tecnologia |
|--------|-----------|
| Linguagem | PHP 7.4 |
| Servidor | Apache (XAMPP) |
| Base de dados | MySQL 5.7+ |
| Front-end | Bootstrap 5 + W3CRM Admin Theme |
| Padrão | MVC personalizado (sem framework externo) |

---

## Estrutura do Projecto

```
sge/
├── app/
│   ├── controllers/        # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── FaturaController.php
│   │   ├── RequisicaoController.php
│   │   ├── FornecedorController.php
│   │   ├── FuncionarioController.php
│   │   ├── MaterialController.php
│   │   ├── RelatorioController.php
│   │   └── StockController.php
│   ├── models/             # Modelos (acesso à BD)
│   │   ├── Fatura.php
│   │   ├── Requisicao.php
│   │   ├── Fornecedor.php
│   │   ├── Funcionario.php
│   │   ├── Material.php
│   │   └── Usuario.php
│   └── views/              # Templates PHP
│       ├── layouts/
│       │   └── main.php    # Layout principal com sidebar
│       ├── auth/
│       │   └── login.php
│       ├── home/
│       │   └── index.php   # Dashboard
│       ├── faturas/
│       ├── requisicoes/
│       ├── fornecedores/
│       ├── funcionarios/
│       ├── materiais/
│       ├── stock/
│       └── relatorios/
├── config/
│   ├── app.example.php     # Configuração da aplicação (modelo)
│   └── database.example.php # Configuração da BD (modelo)
├── core/
│   ├── Controller.php      # Classe base dos controladores
│   ├── Model.php           # Classe base dos modelos (PDO)
│   └── Router.php          # Router URL → Controller::action
├── database/
│   └── sge.sql             # Script de criação das tabelas
├── public/
│   └── theme/              # Assets do tema (CSS, JS, imagens)
│       ├── css/
│       ├── js/
│       ├── vendor/
│       └── images/
├── .htaccess               # Rewrite rules (Apache mod_rewrite)
├── index.php               # Front controller
└── install.php             # Instalador (usar uma vez, depois apagar)
```

---

## Instalação

### Pré-requisitos

- XAMPP com PHP 7.4 e MySQL
- Apache com `mod_rewrite` activado
- Pasta do projecto em `htdocs/sge`

### Passos

**1. Clonar o repositório**

```bash
git clone https://github.com/tesouradanque/SGE.git
# Mover a pasta para htdocs/sge no XAMPP
```

**2. Configurar a base de dados**

```bash
cp config/database.example.php config/database.php
```

Editar `config/database.php` com as credenciais do MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // password do MySQL
define('DB_NAME', 'sge');
```

**3. Configurar a aplicação**

```bash
cp config/app.example.php config/app.php
```

Editar `config/app.php` com o URL correcto:

```php
define('BASE_URL', 'http://localhost/sge');
// Se usar porta diferente, ex: http://localhost:8081/sge
```

**4. Instalar a base de dados**

Aceder no browser:

```
http://localhost/sge/install.php
```

Isto cria todas as tabelas e o utilizador administrador:
- **Email:** `admin@sge.com`
- **Password:** `admin123`

> ⚠️ **Apagar ou bloquear `install.php` após a instalação.**

**5. Activar mod_rewrite no Apache**

No `httpd.conf` do XAMPP, garantir que `AllowOverride All` está activo para a pasta `htdocs`:

```apache
<Directory "C:/xampp/htdocs">
    AllowOverride All
</Directory>
```

**6. Aceder ao sistema**

```
http://localhost/sge
```

---

## Arquitectura MVC

### Routing

O ficheiro `.htaccess` redirige todos os pedidos para `index.php`:

```
GET /sge/faturas/show/3
  → index.php?url=faturas/show/3
  → Router → FaturaController::show(3)
```

**Mapeamento no Router** (`core/Router.php`):

```php
// URLs plurais com mapeamento explícito:
private $map = [
    'faturas'     => 'FaturaController',
    'requisicoes' => 'RequisicaoController',
];

// Outros: ucfirst(segmento) + 'Controller'
// ex: fornecedor → FornecedorController
```

### Controller base (`core/Controller.php`)

Métodos disponíveis em todos os controladores:

| Método | Descrição |
|--------|-----------|
| `$this->view('pasta.ficheiro', $dados)` | Renderiza uma view dentro do layout |
| `$this->redirect('rota')` | Redireccionamento HTTP |
| `$this->post('campo')` | Lê campo POST com trim |
| `$this->flash('tipo', 'msg')` | Armazena mensagem de feedback na sessão |
| `$this->requireAuth()` | Bloqueia acesso se não autenticado |
| `$this->isPost()` | Verifica se o pedido é POST |

### Model base (`core/Model.php`)

Acesso à BD via PDO. Métodos disponíveis:

| Método | Descrição |
|--------|-----------|
| `all($orderBy)` | SELECT * FROM tabela |
| `find($id)` | SELECT por ID |
| `save($dados)` | INSERT |
| `update($id, $dados)` | UPDATE por ID |
| `delete($id)` | DELETE por ID |
| `query($sql, $params)` | Query personalizada |

---

## Base de Dados

### Tabelas

| Tabela | Descrição |
|--------|-----------|
| `fornecedores` | Fornecedores de materiais |
| `materiais` | Catálogo de materiais com stock mínimo |
| `funcionarios` | Motoristas/funcionários que fazem requisições |
| `faturas` | Faturas de compra (entradas de stock) |
| `itens_fatura` | Itens de cada fatura (material + quantidade + preço) |
| `requisicoes` | Requisições de saída de materiais |
| `itens_requisicao` | Itens de cada requisição |
| `usuarios` | Utilizadores do sistema (autenticação) |

### Cálculo de Stock

O stock **não é armazenado** — é calculado em tempo real:

```sql
SELECT
    m.id,
    m.descricao,
    COALESCE(SUM(itf.quantidade), 0)
        - COALESCE(SUM(itr.quantidade), 0) AS stock_actual
FROM materiais m
LEFT JOIN itens_fatura itf ON itf.material_id = m.id
LEFT JOIN itens_requisicao itr ON itr.material_id = m.id
GROUP BY m.id
```

---

## Módulos do Sistema

### Autenticação
- Login com email + password (bcrypt via `password_hash`)
- Sessão PHP — `requireAuth()` bloqueia todas as páginas protegidas
- Route: `GET /auth/login` → `POST /auth/authenticate` → `GET /auth/logout`

### Dashboard (`/`)
- Total de materiais cadastrados
- Materiais com stock abaixo do mínimo (alerta)
- Faturas pendentes de pagamento
- Requisições do mês corrente
- Tabela de alertas de stock crítico
- Últimas 5 faturas e últimas 5 requisições

### Faturas (`/faturas`)
- Listagem com filtro de estado (pendente/pago)
- Criação com múltiplos itens
- Possibilidade de criar novo material inline durante o registo da fatura
- Alteração de estado (pendente ↔ pago)
- Detalhe da fatura com itens

### Requisições (`/requisicoes`)
- Listagem por data
- Criação com validação de stock disponível em tempo real
- Stock disponível mostrado ao seleccionar o material
- Impedimento de requisição acima do stock disponível
- Detalhe com itens e totais

### Materiais (`/material`)
- CRUD completo
- Campos: código, descrição, unidade, stock mínimo, preço unitário padrão
- Stock actual calculado na listagem

### Fornecedores (`/fornecedor`)
- CRUD completo
- Campos: nome, NUIT, telefone, email, endereço

### Funcionários (`/funcionario`)
- CRUD completo
- Campos: nome, cargo, telefone, email

### Stock (`/stock`)
- Visão consolidada do stock actual por material
- Filtro por estado (normal / abaixo do mínimo / esgotado)

### Relatórios (`/relatorios`)
- Relatório de stock actual
- Movimentos por período
- Relatório por material

---

## Tema Visual

O sistema usa o **W3CRM Bootstrap Admin Theme v3.0**.

### Cores configuradas

| Elemento | Cor |
|---------|-----|
| Navigation Header | `color_11` (azul escuro `#0C1A5B`) |
| Sidebar | `color_11` (azul escuro `#0C1A5B`) |
| Header/Topbar | `color_1` (branco) |
| Primary (botões, links) | `color_9` |

Configuradas via `data-*` attributes no `<body>` do layout principal.

### Assets do tema

Localizados em `public/theme/`:
- `css/style.css` — estilos principais do tema
- `css/plugins.css` — estilos dos plugins
- `js/custom.js` — inicialização do tema (MetisMenu, preloader, toggle)
- `vendor/` — bibliotecas (Bootstrap 5, jQuery, MetisMenu, Flatpickr, etc.)

> Os ficheiros fonte originais do tema (`w3crmbootstrap-30/`) **não estão no repositório** (`.gitignore`). Apenas os assets compilados em `public/theme/` são necessários para executar o sistema.

---

## Convenções de Código

### Nomenclatura de views

```
app/views/{pasta_plural}/{ficheiro}.php
```

O controlador deve passar o nome da pasta plural:

```php
$this->view('fornecedores.index', [...]);  // ✓
$this->view('fornecedor.index', [...]);    // ✗ não encontra
```

### Moeda

Todos os valores monetários usam **MT (Metical Moçambicano)** à direita do valor:

```
1.250,00 MT
```

### Datas

- **Formulários:** formato `dd/mm/aaaa` (via Flatpickr)
- **Base de dados:** formato `yyyy-mm-dd` (MySQL DATE)
- Conversão feita nos controllers com `DateTime::createFromFormat('d/m/Y', $data)`

---

## Problemas Conhecidos / Notas Técnicas

### Tema CSS — data-attributes obrigatórios

O tema W3CRM aplica **100% dos estilos via selectores CSS `[data-*=value]`** no `<body>`. Se esses atributos não estiverem presentes, nenhum estilo é aplicado. O layout `main.php` define-os directamente no `<body>` e num script jQuery para garantir que são aplicados mesmo após o carregamento do tema.

### deznav-init.js removido

O ficheiro `deznav-init.js` (painel de personalização do tema) foi **removido do carregamento** porque sobrescrevia os `data-*` attributes definidos no HTML. Em vez disso, um script inline no `main.php` aplica as configurações correctas.

### input[type=date] e calendário sobreposto

O CSS do tema expande o indicador do calendário (`::webkit-calendar-picker-indicator`) com `position: absolute; inset: 0` sem que o input tenha `position: relative`, causando sobreposição sobre toda a área envolvente. Resolvido usando Flatpickr com `input[type=text]` em vez de `input[type=date]`.

---

## Credenciais Padrão

| Campo | Valor |
|-------|-------|
| Email | `admin@sge.com` |
| Password | `admin123` |

> Alterar após a primeira instalação em ambiente de produção.

---

## Licença

Projecto privado — uso interno.
