<?php
	include($_SERVER['DOCUMENT_ROOT']."/php/usuarios.php");


	$user =new usuarios();

	$res=$user->registrar_usuario($_POST);

  	echo json_encode($res);
?>