<?php
	include("../php/usuarios.php");

	$user =new usuarios();

	$res=$user->login($_POST['usuario'],$_POST['pass']);

  	echo json_encode($res);

?>
