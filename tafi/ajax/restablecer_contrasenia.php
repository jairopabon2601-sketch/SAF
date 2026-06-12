<?php

	include("../php/usuarios.php");

	$user =new usuarios();

	$resultado=$user->forgetPassword($_POST['email'],$_POST["codigo_tipo_usuario"]);
	
  	echo json_encode($resultado);

?>