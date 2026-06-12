<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

    $conexion=new conexion_db();
    $retorno="";

    // eliminar solicitud
    $codigo_solicitud = $_POST["codigo_solicitud"];
    $res=$conexion->ejecutar_sql("DELETE FROM tbl_deudores_creditos_solicitudes WHERE codigo_solicitud =".$codigo_solicitud);
    if ($res) {
        $retorno="1";
    }else{
        $retorno="0";
    }

    echo $retorno;
?>