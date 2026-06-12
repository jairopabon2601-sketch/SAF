<?php
    session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

	$conexion=new conexion_db();

    $resultado=$conexion->insertar("tbl_tafi_contactos_web",$_POST);

    if($resultado>0){
        $retorno=array("codigo"=>1,"mensaje"=>"Registro grabado correctamente");
    }else{
        $retorno=array("codigo"=>0,"mensaje"=>"Error al grabar el registro");
    }

    echo json_encode($retorno);
?>