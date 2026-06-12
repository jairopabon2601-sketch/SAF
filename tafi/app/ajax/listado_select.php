<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

	$conexion=new conexion_db();


	$res=$conexion->listado_select($_POST["tabla"],$_POST["valor"],$_POST["etiqueta"],$_POST["filtro"],$_POST["campos_orden"]);
	
	echo json_encode($res);
?>