<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl position-sticky blur shadow-blur mt-4 left-auto top-1 z-index-sticky" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        
        <nav aria-label="breadcrumb">
          <h3 class="font-weight-bolder mb-0" id="nombre_formulario">
          <img x="0px" y="0px"  src="iconos/<?php echo $icono_formulario; ?>" style="width: 35px;"></img>
          <?php echo $nombre_formulario; ?>
          </h3>
        </nav>

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 justify-content-end" id="navbar">
          
          <ul class="navbar-nav  justify-content-end">
            
   
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>

            <div class="dropdown d-none d-xl-block">
              <button class="btn bg-gradient-info dropdown-toggle " type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <span id="span_nombre_usuario"><?php echo $nombre_usuario; ?></span>
              </button>

              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                <li style="margin: 4px;"><a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
                  <span class="d-sm-inline d-none"> <a href="#"> <img src="iconos/gerente.png" class="iconos_menu_session"> <?php echo $nombre_tipo_usuario; ?></a></span>
                </a></li>

                <li style="margin: 4px;"><a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
                  <span class="d-sm-inline d-none"><a href="dashboard.php?proc=11"> <img src="iconos/restablecercontrasena.png" class="iconos_menu_session">Cambiar Contraseña</a></span>
                </a></li>

                <li style="margin: 4px;"><a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
                  <span class="d-sm-inline d-none"><a href="logout.php"> <img src="iconos/cerrar-sesion.png" class="iconos_menu_session"> Cerrar Sesión</a></span>
                </a></li>
              </ul>
            </div>

          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-4" id="div_principal_form">
    </div>
  </main>