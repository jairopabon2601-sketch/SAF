<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$res=$conexion->consultar_campo($_POST["tabla"],$_POST["nombre_campo"],$_POST["filtro"]);

	echo json_encode($res);
?>