<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
    
    <div class="sidenav-header">
      <a class="" href="dashboard.php" >
        <img src="../img/tafi-logo.png" class="" alt="main_logo" style="width: 104px;">
      </a>
    </div>

    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav" id="listado_opciones_menu"></ul>
    </div>

    <div class="sidenav-footer mx-3 card card-frame">
      <ul class="d-xl-none" aria-labelledby="dropdownMenuButton" style="list-style:none;text-decoration: none;">

        <li style="margin: 4px;"><a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
          <span class="d-sm-inline d-none"> <a href="#"> <img src="iconos/gerente.png" class="iconos_menu_session"> <?php echo $_SESSION["nombre_tipo_usuario"]; ?></a></span>
        </a></li>

        <li style="margin: 4px;"><a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
          <span class="d-sm-inline d-none"><a href="dashboard.php?proc=11"> <img src="iconos/restablecercontrasena.png" class="iconos_menu_session">Cambiar Contraseña</a></span>
        </a></li>

        <li style="margin: 4px;"><a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
          <span class="d-sm-inline d-none"><a href="logout.php"> <img src="iconos/cerrar-sesion.png" class="iconos_menu_session"> Cerrar Sesión</a></span>
        </a></li>
      </ul>
    </div>

</aside>