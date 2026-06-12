<?php
  session_start();
  include_once("../conexion/conexion.php");
  include_once("php/usuarios.php");
  include_once("php/funciones.php");

  $codigo_proceso = isset($_GET["proc"]) ? $_GET["proc"] : 0;

  $conexion=new conexion_db();
  $usuario=new usuarios();

  $usuario->validar_session();
  $usuario->validar_permisos($codigo_proceso);

  $datos_formulario=datos_formulario($codigo_proceso);
?>

<!DOCTYPE html>
<html lang="en">

<?php 
  render_template("secciones/head.php", [
  "codigo_origen" => $_SESSION["codigo_origen"],
  "codigo_usuario" => $_SESSION["codigo_usuario"],
  "administrador" => $_SESSION["administrador"],
  "nombre_tipo_usuario" => $_SESSION["nombre_tipo_usuario"],
  "codigo_formulario" => $codigo_proceso
]);
?>

<body class="g-sidenav-show  bg-gray-100">

  <?php render_template("secciones/aside.php",[]); ?>

  <?php render_template("secciones/main.php",[
    "nombre_usuario" => $usuario->nombre_usuario(), 
    "nombre_tipo_usuario" => $_SESSION["nombre_tipo_usuario"], 
    "icono_formulario" => $datos_formulario["icono"],
    "nombre_formulario" => $datos_formulario["nombre"],
    "ruta_formulario" => $datos_formulario["ruta"],
  ]); ?>
  
  <?php render_template("secciones/link_script_footer.php",[]); ?>

</body>

</html>


