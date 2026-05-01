-- ============================================================
-- SGE – Sistema de Gestão de Estoque
-- ============================================================
CREATE DATABASE IF NOT EXISTS sge_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sge_db;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fornecedores (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(150) NOT NULL,
    nif        VARCHAR(30)  DEFAULT NULL,
    telefone   VARCHAR(30)  DEFAULT NULL,
    email      VARCHAR(100) DEFAULT NULL,
    endereco   TEXT         DEFAULT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS materiais (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    codigo                VARCHAR(30)    UNIQUE NOT NULL,
    descricao             VARCHAR(250)   NOT NULL,
    unidade               VARCHAR(20)    NOT NULL DEFAULT 'un',
    preco_unitario_padrao DECIMAL(15,2)  DEFAULT 0.00,
    stock_minimo          DECIMAL(10,2)  DEFAULT 0.00,
    created_at            TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS funcionarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(150) NOT NULL,
    cargo      VARCHAR(80)  DEFAULT NULL,
    telefone   VARCHAR(30)  DEFAULT NULL,
    email      VARCHAR(100) DEFAULT NULL,
    ativo      TINYINT(1)   DEFAULT 1,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS faturas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nr_fatura     VARCHAR(60)  NOT NULL UNIQUE,
    data          DATE         NOT NULL,
    fornecedor_id INT          NOT NULL,
    observacao    TEXT         DEFAULT NULL,
    estado        ENUM('pendente','pago') DEFAULT 'pendente',
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS itens_fatura (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    fatura_id      INT           NOT NULL,
    material_id    INT           NOT NULL,
    quantidade     DECIMAL(12,2) NOT NULL,
    preco_unitario DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (fatura_id)   REFERENCES faturas(id)   ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiais(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS requisicoes (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nr_requisicao  VARCHAR(60) NOT NULL UNIQUE,
    data           DATE        NOT NULL,
    funcionario_id INT         NOT NULL,
    observacao     TEXT        DEFAULT NULL,
    created_at     TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS itens_requisicao (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    requisicao_id  INT           NOT NULL,
    material_id    INT           NOT NULL,
    quantidade     DECIMAL(12,2) NOT NULL,
    preco_unitario DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (requisicao_id) REFERENCES requisicoes(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id)   REFERENCES materiais(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(150) NOT NULL,
    email      VARCHAR(100) UNIQUE NOT NULL,
    senha      VARCHAR(255) NOT NULL,
    perfil     ENUM('admin','operador') DEFAULT 'operador',
    ativo      TINYINT(1)   DEFAULT 1,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ajustes_estoque (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT           NOT NULL,
    tipo        ENUM('entrada','saida') NOT NULL,
    quantidade  DECIMAL(12,2) NOT NULL,
    motivo      TEXT          NOT NULL,
    usuario_id  INT           NOT NULL,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materiais(id),
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ============================================================
-- Migration notes (run on existing databases):
-- ALTER TABLE faturas ADD UNIQUE (nr_fatura);
-- ============================================================
