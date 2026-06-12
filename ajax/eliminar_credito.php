<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

    $conexion=new conexion_db();
    $retorno="";

    // eliminar creditos

    $codigo_credito = $_POST["codigo_credito"];
    $res=$conexion->ejecutar_sql("DELETE FROM tbl_deudores_creditos WHERE codigo_credito =".$codigo_credito);
    if ($res) {
        $result = $conexion->ejecutar_sql("DELETE FROM tbl_deudores_creditos_cuotas WHERE codigo_credito =".$codigo_credito);
        if ($result) {
            $retorno="1";
        }else{
            $retorno="0";
        }
    }else{
        $retorno="0";
    }

    echo $retorno;
?>