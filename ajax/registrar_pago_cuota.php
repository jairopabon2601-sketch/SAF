<?php
	
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$resultado="";

	$campos=array();
	$campos["estado"]=1;
	$campos["fecha_pago"]=$_POST["fecha_registro_pago"];
	$campos["valor_pagado"]=$_POST["valor_pagado"];
	$campos["detalle"]=$_POST["detalle"];
	$campos["usuario_registro_pago"]=$_SESSION["codigo_usuario"];

	$res=$conexion->actualizar("tbl_ahorradores_cuotas",$campos,"codigo_cuota='".$_POST["codigo_cuota"]."'");

	if ($res) {
		$resultado="Pago Registrado.";
	}else{
		$resultado="No hubo cambios que guardar.";
	}

	echo $resultado;

?>