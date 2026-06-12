<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$con=new conexion_db();

    $campos = $_POST;   
    $filtro = "codigo_solicitud=".$_POST["codigo_solicitud"];

    $resultado=$con->actualizar("tbl_deudores_creditos_solicitudes",$campos,$filtro);

    if ($resultado) {
        $retorno["resultado"]=1;
        $retorno["mensaje"]="Se actualizó la solicitud de crédito.";
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se pudo actualizar la solicitud de crédito.";
    }

	echo json_encode($retorno);

?>