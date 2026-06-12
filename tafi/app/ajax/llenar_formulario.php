<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

	$conexion=new conexion_db();

    $tabla = $_POST["tabla"];
    $filtro = $_POST["filtro"];

    $sql = "SELECT * FROM $tabla WHERE $filtro";

    $res_consulta=$conexion->ejecutar_sql($sql);

		if ($res_consulta->num_rows>0) {
				
			$datos_consulta=$res_consulta->fetch_all(MYSQLI_ASSOC);
			$retorno["datos"]=$datos_consulta;
			$retorno["resultado"]=1;

		}else{
			$retorno["resultado"]=0;
			$retorno["mensaje"]="No se encontraron registros de la consulta.";
		}

    echo json_encode($retorno);

?>