<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();    
    $retorno=array();


    $resultado=$conexion->borrar("tbl_tafi_torneos_sedes", "codigo_sede='".$_POST["codigo_sede"]."'");

    if($resultado){
        $retorno["resultado"]=1;
        $retorno["mensaje"]="Sede eliminada correctamente";
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="Error al eliminar la sede";
    }

    echo json_encode($retorno);
?>