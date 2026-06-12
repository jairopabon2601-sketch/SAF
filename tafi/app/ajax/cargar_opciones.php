<?php
	session_start();
	include_once("../../conexion/conexion.php");

	$conexion=new conexion_db();


	if ( $_SESSION["administrador"] != 1) {
		$sql.="SELECT 
		pro.*
		FROM tbl_tafi_procesos pro
		
		INNER JOIN tbl_tafi_usuarios_procesos up ON 
		pro.codigo_proceso=up.codigo_proceso
		
		WHERE pro.codigo_estado=1 
		AND pro.listado_menu=1
		AND up.codigo_tipo_usuario=".$_SESSION["codigo_tipo_usuario"]."
		
		GROUP BY pro.codigo_proceso
		ORDER BY pro.orden";
	}else{
		$sql.="SELECT 
		pro.*
		FROM tbl_tafi_procesos pro
		WHERE pro.codigo_estado=1 
		AND pro.listado_menu=1
		GROUP BY pro.codigo_proceso
		ORDER BY pro.orden";
	}	


	$resultado=$conexion->ejecutar_sql($sql);
	
	if($resultado->num_rows > 0){
		$resultado = $resultado->fetch_all(MYSQLI_ASSOC);

		$retorno["opciones"]=$resultado;
		$retorno["resultado"]=1;
	}else{
		$retorno["resultado"]=0;
	}

	echo json_encode($retorno);

?>