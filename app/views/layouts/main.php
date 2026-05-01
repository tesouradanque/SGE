<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SGE – Sistema de Gestão de Estoque</title>
  <link rel="shortcut icon" type="image/png" href="<?= BASE_URL ?>/public/theme/images/favicon.png">
  <link href="<?= BASE_URL ?>/public/theme/vendor/metismenu/dist/metisMenu.min.css?v=3" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/theme/vendor/bootstrap-select/dist/css/bootstrap-select.min.css?v=3" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/theme/css/plugins.css?v=3" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/theme/css/style.css?v=3" rel="stylesheet">
  <style>
    .badge-ok  { background: #3ac977; color: #fff; }
    .badge-low { background: #f0a500; color: #fff; }
    .badge-out { background: #f72b50; color: #fff; }
    h5.page-title { font-size: 1.1rem; font-weight: 600; color: var(--bs-heading-color); }
  </style>
</head>
<body
  data-theme-version="light"
  data-layout="vertical"
  data-nav-headerbg="color_11"
  data-headerbg="color_1"
  data-sidebar-style="full"
  data-sidebarbg="color_11"
  data-sidebar-position="fixed"
  data-header-position="fixed"
  data-container="wide"
  data-primary="color_9"
  data-typography="poppins">

  <div id="preloader">
    <div class="lds-ripple"><div></div><div></div></div>
  </div>

  <div id="main-wrapper" class="show">

    <!-- Nav Header -->
    <div class="nav-header">
      <a href="<?= BASE_URL ?>/home" class="brand-logo" aria-label="SGE">
        <svg class="logo-abbr" width="39" height="23" viewBox="0 0 39 23" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M32.0362 22H19.0466L20.7071 18.7372C20.9559 18.2484 21.455 17.9378 22.0034 17.9305L31.1036 17.8093C33.0753 17.6497 33.6571 15.9246 33.7015 15.0821C33.7015 13.2196 32.1916 12.5765 31.4367 12.4878H23.7095L25.9744 8.49673H30.4375C31.8763 8.3903 32.236 7.03332 32.236 6.36814C32.3426 4.93133 30.9482 4.61648 30.2376 4.63865H28.6955C28.2646 4.63865 27.9788 4.19212 28.1592 3.8008L29.7047 0.44798C31.0903 0.394765 32.8577 0.780573 33.5683 0.980129C38.6309 3.42801 37.0988 7.98676 35.6999 9.96014C38.1513 11.9291 38.4976 14.3282 38.3644 15.2816C38.098 20.1774 34.0346 21.8005 32.0362 22Z" fill="var(--bs-primary)"/>
          <path d="M9.89261 21.4094L0 2.80536H4.86354C5.41354 2.80536 5.91795 3.11106 6.17246 3.59864L12.4032 15.5355C12.6333 15.9762 12.6261 16.5031 12.3842 16.9374L9.89261 21.4094Z" fill="#fff"/>
          <path d="M17.5705 21.4094L7.67786 2.80536H12.5372C13.0894 2.80536 13.5954 3.11351 13.8489 3.60412L20.302 16.0939L17.5705 21.4094Z" fill="#fff"/>
          <path d="M17.6443 21.4094L28.2751 0H23.4513C22.8806 0 22.361 0.328884 22.1168 0.844686L14.8271 16.2416L17.6443 21.4094Z" fill="#fff"/>
        </svg>
        <svg class="brand-title" width="32" height="16" viewBox="0 0 32 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <text x="0" y="13" font-size="14" font-weight="bold" fill="#fff" font-family="Arial">SGE</text>
        </svg>
      </a>
      <div class="nav-control">
        <div class="hamburger">
          <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
      </div>
    </div>

    <!-- Header -->
    <div class="header">
      <div class="header-content">
        <nav class="navbar navbar-expand">
          <div class="collapse navbar-collapse justify-content-between">
            <div class="header-left">
              <span class="text-muted small"><?= date('d/m/Y') ?></span>
            </div>
            <ul class="navbar-nav header-right align-items-center gap-3">
              <li class="nav-item">
                <span class="small">
                  <i class="fas fa-user-circle me-1 text-primary"></i>
                  <?= htmlspecialchars($_SESSION['usuario']['nome'] ?? '') ?>
                  <span class="badge bg-primary ms-1"><?= ucfirst($_SESSION['usuario']['perfil'] ?? '') ?></span>
                </span>
              </li>
              <li class="nav-item">
                <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-sm btn-outline-secondary">
                  <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
    </div>

    <!-- Sidebar -->
    <?php $seg = explode('/', trim($_GET['url'] ?? '', '/')); $ctrl = strtolower($seg[0] ?? 'home'); ?>
    <div class="deznav">
      <div class="deznav-scroll">
        <ul class="metismenu" id="menu">

          <li class="menu-title">PRINCIPAL</li>

          <li class="<?= $ctrl === 'home' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/home" aria-expanded="false">
              <div class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2.5 7.5L10 1.66667L17.5 7.5V16.6667C17.5 17.1087 17.3244 17.5326 17.0118 17.8452C16.6993 18.1577 16.2754 18.3333 15.8333 18.3333H4.16667C3.72464 18.3333 3.30072 18.1577 2.98816 17.8452C2.67559 17.5326 2.5 17.1087 2.5 16.6667V7.5Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M7.5 18.3333V10H12.5V18.3333" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Dashboard</span>
            </a>
          </li>

          <li class="menu-title">MOVIMENTOS</li>

          <li class="<?= $ctrl === 'faturas' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/faturas" aria-expanded="false">
              <div class="menu-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M13.5096 2.53165H7.41104C5.50437 2.52432 3.94146 4.04415 3.89654 5.9499V15.7701C3.85437 17.7071 5.38979 19.3121 7.32671 19.3552H14.7343C16.6538 19.2773 18.1663 17.6915 18.1525 15.7701V7.36798L13.5096 2.53165Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.2688 2.52V5.18742C13.2688 6.48909 14.3211 7.54417 15.6228 7.54784H18.1482" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.0974 14.0786H8.1474" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M11.2229 10.6388H8.14655" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Entradas (Faturas)</span>
            </a>
          </li>

          <li class="<?= $ctrl === 'requisicoes' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/requisicoes" aria-expanded="false">
              <div class="menu-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6.64111 13.5497L9.38482 9.9837L12.5145 12.4421L15.1995 8.97684" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <ellipse cx="18.3291" cy="3.85021" rx="1.76201" ry="1.76201" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.6808 2.86012H7.01867C4.25818 2.86012 2.54651 4.81512 2.54651 7.57561V14.9845C2.54651 17.7449 4.22462 19.6915 7.01867 19.6915H14.9058C17.6663 19.6915 19.3779 17.7449 19.3779 14.9845V8.53213" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Saídas (Requisições)</span>
            </a>
          </li>

          <li class="<?= $ctrl === 'stock' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/stock" aria-expanded="false">
              <div class="menu-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6.75713 9.35157V15.64" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M11.0349 6.34253V15.64" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M15.2428 12.6746V15.64" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M15.2952 1.83333H6.70474C3.7103 1.83333 1.83331 3.95274 1.83331 6.95306V15.0469C1.83331 18.0473 3.70157 20.1667 6.70474 20.1667H15.2952C18.2984 20.1667 20.1666 18.0473 20.1666 15.0469V6.95306C20.1666 3.95274 18.2984 1.83333 15.2952 1.83333Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Stock Actual</span>
            </a>
          </li>

          <li class="menu-title">CADASTROS</li>

          <li class="<?= $ctrl === 'material' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/material" aria-expanded="false">
              <div class="menu-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M14.9732 2.52102H7.0266C4.25735 2.52102 2.52118 4.48177 2.52118 7.25651V14.7438C2.52118 17.5186 4.2491 19.4793 7.0266 19.4793H14.9723C17.7507 19.4793 19.4795 17.5186 19.4795 14.7438V7.25651C19.4795 4.48177 17.7507 2.52102 14.9732 2.52102Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M7.73657 11.0002L9.91274 13.1754L14.2632 8.82493" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Materiais</span>
            </a>
          </li>

          <li class="<?= $ctrl === 'fornecedor' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/fornecedor" aria-expanded="false">
              <div class="menu-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M8.79222 13.9396C12.1738 13.9396 15.0641 14.452 15.0641 16.4989C15.0641 18.5458 12.1931 19.0729 8.79222 19.0729C5.40972 19.0729 2.52039 18.5651 2.52039 16.5172C2.52039 14.4694 5.39047 13.9396 8.79222 13.9396Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M8.79223 11.0182C6.57206 11.0182 4.77173 9.21874 4.77173 6.99857C4.77173 4.7784 6.57206 2.97898 8.79223 2.97898C11.0115 2.97898 12.8118 4.7784 12.8118 6.99857C12.8201 9.21049 11.0326 11.0099 8.82064 11.0182H8.79223Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M15.1095 9.9748C16.5771 9.76855 17.7073 8.50905 17.7101 6.98464C17.7101 5.48222 16.6147 4.23555 15.1782 3.99997" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M17.0458 13.5045C18.4675 13.7163 19.4603 14.2149 19.4603 15.2416C19.4603 15.9483 18.9928 16.4067 18.2374 16.6936" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Fornecedores</span>
            </a>
          </li>

          <li class="<?= $ctrl === 'funcionario' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/funcionario" aria-expanded="false">
              <div class="menu-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M10.986 14.0673C7.4407 14.0673 4.41309 14.6034 4.41309 16.7501C4.41309 18.8969 7.4215 19.4521 10.986 19.4521C14.5313 19.4521 17.5581 18.9152 17.5581 16.7693C17.5581 14.6234 14.5505 14.0673 10.986 14.0673Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M10.986 11.0054C13.3126 11.0054 15.1983 9.11881 15.1983 6.79223C15.1983 4.46564 13.3126 2.57993 10.986 2.57993C8.65944 2.57993 6.77285 4.46564 6.77285 6.79223C6.76499 9.11096 8.63849 10.9975 10.9563 11.0054H10.986Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Funcionários</span>
            </a>
          </li>

          <li class="menu-title">ANÁLISE</li>

          <li class="<?= $ctrl === 'relatorio' ? 'mm-active' : '' ?>">
            <a href="<?= BASE_URL ?>/relatorio" aria-expanded="false">
              <div class="menu-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M13.5096 2.53165H7.41104C5.50437 2.52432 3.94146 4.04415 3.89654 5.9499V15.7701C3.85437 17.7071 5.38979 19.3121 7.32671 19.3552H14.7343C16.6538 19.2773 18.1663 17.6915 18.1525 15.7701V7.36798L13.5096 2.53165Z" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.2688 2.52V5.18742C13.2688 6.48909 14.3211 7.54417 15.6228 7.54784H18.1482" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.0974 14.0786H8.1474" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M11.2229 10.6388H8.14655" stroke="#888" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="nav-text">Relatórios</span>
            </a>
          </li>

        </ul>
      </div>
    </div>

    <!-- Content Body -->
    <main class="content-body">

      <?php if (!empty($_SESSION['flash'])): ?>
      <div class="px-4 pt-4">
        <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_SESSION['flash']['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      </div>
      <?php unset($_SESSION['flash']); endif; ?>

      <div class="p-4">
        <?= $content ?>
      </div>

    </main>

  </div><!-- /#main-wrapper -->

  <script src="<?= BASE_URL ?>/public/theme/vendor/jquery/dist/jquery.min.js?v=3"></script>
  <script src="<?= BASE_URL ?>/public/theme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js?v=3"></script>
  <script src="<?= BASE_URL ?>/public/theme/vendor/bootstrap-select/dist/js/bootstrap-select.min.js?v=3"></script>
  <script src="<?= BASE_URL ?>/public/theme/vendor/metismenu/dist/metisMenu.min.js?v=3"></script>
  <script src="<?= BASE_URL ?>/public/theme/js/custom.js?v=3"></script>
  <script>
  jQuery(document).ready(function($) {
    // Forçar atributos de cor no body
    $('body')
      .attr('data-theme-version', 'light')
      .attr('data-layout', 'vertical')
      .attr('data-nav-headerbg', 'color_11')
      .attr('data-headerbg', 'color_1')
      .attr('data-sidebar-style', 'full')
      .attr('data-sidebarbg', 'color_11')
      .attr('data-sidebar-position', 'fixed')
      .attr('data-header-position', 'fixed')
      .attr('data-container', 'wide')
      .attr('data-primary', 'color_9')
      .attr('data-typography', 'poppins');

    // Preloader
    setTimeout(function() {
      $('#preloader').remove();
      $('#main-wrapper').addClass('show');
    }, 300);

    // MetisMenu
    if ($('#menu').length) { $('#menu').metisMenu(); }

    // Hamburger toggle
    $('.nav-control').on('click', function() {
      $('#main-wrapper').toggleClass('menu-toggle');
      $('.hamburger').toggleClass('is-active');
    });

    // Auto-fechar flash messages
    setTimeout(function() {
      $('.alert.show').each(function() {
        try { bootstrap.Alert.getOrCreateInstance(this).close(); } catch(e) {}
      });
    }, 4000);
  });
  </script>
</body>
</html>
