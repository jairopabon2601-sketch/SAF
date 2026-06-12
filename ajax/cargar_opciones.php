<?php
	session_start();
	include_once("../conexion/conexion.php");

	$conexion=new conexion_db();

	$sql="SELECT 
	
	pro.* 

	FROM tbl_procesos_perfiles pp

	inner join tbl_procesos pro on 
	pp.codigo_proceso=pro.codigo_proceso

	where pro.codigo_estado=1
	AND pro.listado_menu=1 ";

	if ( $_SESSION["codigo_perfil"] != 6 ) {
		$sql.= "AND pp.codigo_perfil=".$_SESSION["codigo_perfil"] ;
	}	

	$sql.=" GROUP by pro.codigo_proceso";


	$sql.=" ORDER BY pro.nombre";

	

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