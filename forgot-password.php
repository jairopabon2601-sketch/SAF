<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Restablecer Contraseña</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>

<script src="js/jquery.growl.js" type="text/javascript"></script>
<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
  <?php
     if (isset($_GET["form"])) {
       echo "var codigo_formulario=".$_GET["form"].";";
     }else{
       echo "var codigo_formulario=0";
     }

   ?>

</script>

  <script src="js/saf.js"></script>

  <script type="text/javascript">
    $(document).ready(function(){
        $("#btn_restablecer_contra").bind("click",validar);
        rellenar_select("tbl_usuarios_tipos","codigo_tipo_usuario","nombre","","codigo_tipo_usuario");
    });

    function validar(){
      if ($("#exampleInputEmail").val()=="") {
        alert("Debe agregar su correo");
        return false;
      }else{
        $.ajax({
        type: 'POST',
        async: false,
        url: 'ajax/restablecer_contrasenia.php',
        data: {
          email: $("#exampleInputEmail").val(),
          codigo_tipo_usuario: $("#codigo_tipo_usuario").val()
        },
        success: function(data){
          console.log(data);
          if (data.resultado==0) {
            $.growl.error({ message:data.mensaje });
          }else{
            $.growl.notice({ message:data.mensaje });
            //location.href="login.html";
          }  
        },
        dataType: 'json'
      });
      }
    }
  </script>
</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-6 col-lg-6 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-2">¿Olvidaste tu contraseña?</h1>
                    <p class="mb-4">Ingrese su dirección de correo electrónico a continuación y le enviaremos un enlace para restablecer su contraseña.</p>
                  </div>
                  <form class="user">
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Email...">
                    </div>

                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Tipo Usuario:</span>
                      </div>
                      <select class="form-control" name="codigo_tipo_usuario" id="codigo_tipo_usuario"></select>    
                    </div>


                    <a href="#" class="btn btn-primary btn-user btn-block" id="btn_restablecer_contra">
                      Restablecer la contraseña
                    </a>
                  </form>
                 
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>


  </script>

</body>

</html>
