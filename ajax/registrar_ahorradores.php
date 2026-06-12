<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$retorno="";

	$campos=array();
	$campos["codigo_asesor"]=$_POST["codigo_asesor"];
	$campos["num_documento"]=$_POST["num_documento"];
	$campos["nombres"]=$_POST["nombres"];
	$campos["apellidos"]=$_POST["apellidos"]; 
	$campos["direccion"]=$_POST["direccion"];
	$campos["telefono"]=$_POST["telefono"];

	$res=$conexion->insertar("tbl_ahorradores",$campos);	

	if ($res) {
		$retorno="Ahorrador registrado";	
	}else{
		$retorno="No se registro el ahorrador.";	
	}

	echo $retorno;
?>