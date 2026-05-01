<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login – SGE</title>
  <link rel="shortcut icon" type="image/png" href="<?= BASE_URL ?>/public/theme/images/favicon.png">
  <link href="<?= BASE_URL ?>/public/theme/vendor/metismenu/dist/metisMenu.min.css?v=3" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/theme/vendor/bootstrap-select/dist/css/bootstrap-select.min.css?v=3" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/theme/css/plugins.css?v=3" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/theme/css/style.css?v=3" rel="stylesheet">
</head>
<body
  data-theme-version="light"
  data-layout="vertical"
  data-nav-headerbg="color_1"
  data-headerbg="color_1"
  data-sidebar-style="full"
  data-sidebarbg="color_1"
  data-typography="poppins">

  <div class="auth-wrapper">
    <div class="row">

      <!-- Left panel -->
      <div class="col-xl-6 col-lg-6 order-lg-1">
        <div class="auth-info text-center">
          <div class="mb-5 mx-auto col-xxl-6">
            <div class="brand-logo mb-3">
              <svg width="39" height="23" viewBox="0 0 39 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32.0362 22H19.0466L20.7071 18.7372C20.9559 18.2484 21.455 17.9378 22.0034 17.9305L31.1036 17.8093C33.0753 17.6497 33.6571 15.9246 33.7015 15.0821C33.7015 13.2196 32.1916 12.5765 31.4367 12.4878H23.7095L25.9744 8.49673H30.4375C31.8763 8.3903 32.236 7.03332 32.236 6.36814C32.3426 4.93133 30.9482 4.61648 30.2376 4.63865H28.6955C28.2646 4.63865 27.9788 4.19212 28.1592 3.8008L29.7047 0.44798C31.0903 0.394765 32.8577 0.780573 33.5683 0.980129C38.6309 3.42801 37.0988 7.98676 35.6999 9.96014C38.1513 11.9291 38.4976 14.3282 38.3644 15.2816C38.098 20.1774 34.0346 21.8005 32.0362 22Z" fill="var(--bs-primary)"/>
                <path d="M9.89261 21.4094L0 2.80536H4.86354C5.41354 2.80536 5.91795 3.11106 6.17246 3.59864L12.4032 15.5355C12.6333 15.9762 12.6261 16.5031 12.3842 16.9374L9.89261 21.4094Z" fill="var(--bs-heading-color)"/>
                <path d="M17.5705 21.4094L7.67786 2.80536H12.5372C13.0894 2.80536 13.5954 3.11351 13.8489 3.60412L20.302 16.0939L17.5705 21.4094Z" fill="var(--bs-heading-color)"/>
                <path d="M17.6443 21.4094L28.2751 0H23.4513C22.8806 0 22.361 0.328884 22.1168 0.844686L14.8271 16.2416L17.6443 21.4094Z" fill="var(--bs-heading-color)"/>
              </svg>
            </div>
            <h4 class="mb-1">SGE</h4>
            <p class="info-text">Sistema de Gestão de Estoque — controlo de entradas, saídas e stock em tempo real.</p>
          </div>
          <div class="auth-media">
            <img class="w-75 img-fluid" src="<?= BASE_URL ?>/public/theme/images/login.png" alt="">
          </div>
        </div>
      </div>

      <!-- Right panel: form -->
      <div class="col-xl-6 col-lg-6 mx-auto align-self-center">
        <div class="auth-form">
          <div class="text-center mb-4">
            <h3 class="mb-0">Entrar</h3>
            <p class="mb-0">Faça login para continuar</p>
          </div>

          <?php if (!empty($erro)): ?>
            <div class="alert alert-danger py-2 text-center small"><?= htmlspecialchars($erro) ?></div>
          <?php endif; ?>

          <form action="<?= BASE_URL ?>/auth/authenticate" method="POST">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control form-control-lg"
                placeholder="admin@sge.com" required autofocus>
            </div>
            <div class="mb-4">
              <label class="form-label">Password</label>
              <div class="position-relative">
                <input type="password" name="senha" autocomplete="current-password"
                  class="form-control form-control-lg dz-password" placeholder="••••••••" required>
                <span class="show-pass position-absolute top-50 end-0 me-2 translate-middle">
                  <span class="show"><i class="fa fa-eye-slash"></i></span>
                  <span class="hide"><i class="fa fa-eye"></i></span>
                </span>
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Entrar
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>

  <script src="<?= BASE_URL ?>/public/theme/vendor/jquery/dist/jquery.min.js?v=3"></script>
  <script src="<?= BASE_URL ?>/public/theme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js?v=3"></script>
  <script src="<?= BASE_URL ?>/public/theme/vendor/metismenu/dist/metisMenu.min.js?v=3"></script>
  <script src="<?= BASE_URL ?>/public/theme/js/custom.js?v=3"></script>
</body>
</html>
