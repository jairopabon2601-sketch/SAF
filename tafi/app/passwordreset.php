<?php
    session_start();
    if (isset($_SESSION['codigo_usuario'])){
        header("Location: dashboard.php");
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <title>
    TAFI
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="assets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="">

  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-75">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
              <div class="card card-plain mt-8">
                <div class="card-header pb-0 text-left bg-transparent">
                  <h3 class="font-weight-bolder text-info text-gradient">Recupera tu cuenta</h3>
                </div>
                <div class="card-body">
                  <form role="form">
                    <label>Ingresa tu correo electrónico para buscar tu cuenta.</label>
                    
                    <div class="mb-3">
                      <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Correo Electrónico" aria-label="Email" aria-describedby="email-addon">
                    </div>

                    <label>Selecciona el tipo de Usuario</label>
                    
                    <div class="mb-3">
                        <select class="form-control" name="codigo_tipo_usuario" id="codigo_tipo_usuario" type="text"></select>
                    </div>

                   
                    <div class="text-center">
                      <button type="button" id="btnLogin" class="btn bg-gradient-info w-100 mt-3 mb-0">Buscar</button>
                      <button type="button" id="btnCancelar" class="btn bg-gradient-info w-100 mt-2 mb-0">Cancelar</button>
                    </div>

                  </form>
                </div>
                <!--<div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-sm mx-auto">
                    Don't have an account?
                    <a href="javascript:;" class="text-info text-gradient font-weight-bold">Sign up</a>
                  </p>-->
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <footer class="footer py-5">
    <div class="container">
     
      <div class="row">
        <div class="col-8 mx-auto text-center mt-1">
          <p class="mb-0 text-secondary">
            Copyright © <script>
              document.write(new Date().getFullYear())
            </script> TAFI
          </p>
        </div>
      </div>
    </div>
  </footer>
  
  <!--   Core JS Files   -->
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>

  <script
  src="https://code.jquery.com/jquery-3.7.0.js"
  integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
  crossorigin="anonymous"></script>

  <script src="../js/funciones_tafi.js"></script>


  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    $(document).ready(function(){
        rellenar_select("tbl_tafi_usuarios_tipos","codigo_tipo_usuario","nombre","permite_recuperar_pass=1","codigo_tipo_usuario","","nombre");

        $("#btnCancelar").bind("click",function(){
            window.location.href="index.php";
        });
    });

    function login(){
      
      if($("#usuario").val()!="" || $("#password").val()!=""){
        
        $.ajax({
          url:"ajax/login.php",
          type:"POST",
          dataType:"json",
          data: {
            usuario:$("#usuario").val(),
            pass:$("#password").val()
          },
          success:function(respuesta){


            if(respuesta.resultado==1){

              if(respuesta.proceso_inicio>0){
                window.location.href="dashboard.php?proc="+respuesta.proceso_inicio;
              }else{
                window.location.href="dashboard.php";
              }
              
            }else{
              alert("Usuario o contraseña incorrectos");
            }
          }
        });

      }else{
        alert("Debe ingresar usuario y contraseña");
      }
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/soft-ui-dashboard.min.js?v=1.0.7"></script>
</body>

</html>