<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HERROR</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <link rel="stylesheet" href="public/css/style.css?v=<?php echo time(); ?>">

  <style>
    /* Estilos solo para móvil: Menú tipo "cortina" para no aplastar el logo */
    @media (max-width: 991.98px) {
        #menuPrincipal {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: var(--color-principal);
            z-index: 1000;
            padding-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .navbar-custom .nav-link {
            padding: 10px 20px !important;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Ajuste del contador del carrito en móvil para que no se corte */
        #contador-carrito {
            transform: translate(-30%, -20%) !important;
        }
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <!-- TU CONTENEDOR ORIGINAL INTACTO -->
    <div class="container-fluid px-4 px-lg-5 position-relative">

      <button class="navbar-toggler border-0 p-0 shadow-none text-white" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
        <span class="bi bi-list fs-3"></span>
      </button>

      <div class="collapse navbar-collapse" id="menuPrincipal">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-3 mt-lg-0">
          <li class="nav-item"><a class="nav-link" href="../catalogo.php">Catalogo</a></li>
          <li class="nav-item"><a class="nav-link" href="catalogo.php?genero=1">Hombre</a></li>
          <li class="nav-item"><a class="nav-link" href="catalogo.php?genero=2">Mujer</a></li>
          <li class="nav-item"><a class="nav-link text-danger fw-bold" href="../citas.php">Pide Cita</a></li>
          <li class="nav-item"><a class="nav-link text-danger fw-bold" href="../segundaMano.php">Segunda Mano</a></li>
          <li class="nav-item"><a class="nav-link text-danger fw-bold" href="../contacto.php">Contacto</a></li>
        </ul>
      </div>

      <a class="navbar-brand position-absolute top-50 start-50 translate-middle m-0 fw-bold fs-4 text-uppercase ls-2" href="index.php">
        HERROR
      </a>

      <!-- TUS ICONOS DE LA DERECHA INTACTOS -->
      <div class="d-flex gap-3 align-items-center order-lg-last ms-auto">
        
        <div class="search-wrapper d-flex align-items-center position-relative m-0">
          <a href="#" class="text-reset search-icon-btn"><i class="bi bi-search fs-5"></i></a>

          <div class="search-input-container">
            <input type="text" id="inputBuscador" class="form-control rounded-0 border-dark border-2 text-uppercase fw-bold" placeholder="BUSCAR PRENDA..." autocomplete="off">

            <div id="cajaResultados" class="position-absolute w-100 bg-white border border-dark border-2 shadow-lg d-none" style="top: 100%; left: 0; z-index: 1000; max-height: 400px; overflow-y: auto;">
            </div>
          </div>
        </div>

        <?php if (!isset($_SESSION['usuario_id'])) { ?>
          <a href="#" class="text-reset" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="bi bi-person fs-5"></i>
          </a>
        <?php } else { ?>
          <div class="dropdown">
            <a href="#" class="text-white text-decoration-none dropdown-toggle text-uppercase fw-bold d-flex align-items-center gap-1" style="font-size: 0.85rem;" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-fill fs-5"></i> <span class="d-none d-md-inline">Hola, <?php echo $_SESSION['nombre']; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end rounded-0 border-dark mt-3 position-absolute" aria-labelledby="dropdownUser">
              <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
              <li><a class="dropdown-item" href="perfil.php?seccion=pedidos">Mis Pedidos</a></li>
              <li><a class="dropdown-item" href="perfil.php?seccion=favoritos">Mis Favoritos</a></li>

              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item text-danger fw-bold" href="controllers/usuarioController.php?accion=logout">Cerrar Sesión</a></li>
            </ul>
          </div>
        <?php }; ?>
        
        <a href="carrito.php" class="text-reset position-relative">
          <i class="bi bi-bag fs-5"></i>
          <span id="contador-carrito" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
            <?php
            if (isset($_SESSION['carrito'])) {
              $totalArticulos = 0;
              foreach ($_SESSION['carrito'] as $item) {
                $totalArticulos += $item['cantidad'];
              }
              echo $totalArticulos;
            } else {
              echo 0;
            }
            ?>
          </span>
        </a>
      </div>

    </div>
  </nav>

  <!-- EL RESTO DE TU CÓDIGO INFERIOR SE QUEDA IGUAL (Modal de usuario) -->
  <?php if (!isset($_SESSION['usuario_id'])) { ?>
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
        <div class="modal-content rounded-0 border-0 p-0 overflow-hidden shadow-lg">

          <div class="anim-container">

            <div class="form-box Login">
              <h2>Entrar</h2>
              <form action="controllers/usuarioController.php" method="POST">
                <div class="input-box">
                  <input type="email" name="email" required>
                  <label>Email</label>
                  <i class="bi bi-envelope"></i>
                </div>
                <div class="input-box">
                  <input type="password" name="password" required>
                  <label>Contraseña</label>
                  <i class="bi bi-lock"></i>
                </div>
                <button class="btn-anim" type="submit" name="accion" value="login">Iniciar Sesión</button>
                <div class="regi-link">
                  <p>¿No te acuerdas de la contraseña? <br> <a href="recuperarPassword.php">Recuperar Contraseña</a></p>
                </div>
                <div class="regi-link">
                  <p>¿No tienes cuenta? <a href="#" class="SignUpLink">Regístrate</a></p>
                </div>
              </form>
            </div>

            <div class="info-content Login">
              <h2>¡HOLA!</h2>
              <p>Inicia sesión para acceder a tu historial de pedidos, guardar tus favoritos y descubrir las últimas novedades.</p>
            </div>

            <div class="form-box Register">
              <h2>Registro</h2>
              <form action="controllers/usuarioController.php" method="POST">
                <div class="input-box">
                  <input type="text" name="nombre" required>
                  <label>Nombre</label>
                  <i class="bi bi-person"></i>
                </div>
                <div class="input-box">
                  <input type="text" name="apellidos" required>
                  <label>Apellidos</label>
                  <i class="bi bi-people"></i>
                </div>
                <div class="input-box">
                  <input type="email" name="email" required>
                  <label>Email</label>
                  <i class="bi bi-envelope"></i>
                </div>
                <div class="input-box">
                  <input type="password" name="password" required>
                  <label>Contraseña</label>
                  <i class="bi bi-lock"></i>
                </div>
                <button class="btn-anim" type="submit" name="accion" value="registro">Crear Cuenta</button>
                <div class="regi-link">
                  <p>¿Ya tienes cuenta? <a href="#" class="SignInLink">Inicia Sesión</a></p>
                </div>
              </form>
            </div>

            <div class="info-content Register">
              <h2>¡ÚNETE!</h2>
              <p>Crea tu cuenta ahora para disfrutar de una experiencia de compra rápida y gestionar tu armario virtual.</p>
            </div>

          </div>

        </div>
      </div>
    </div>
  <?php }; ?>