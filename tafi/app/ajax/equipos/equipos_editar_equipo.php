<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db(); 
    
    $datos=array();
    $datos["nombre_equipo"]=$_POST["nombre_equipo"];

    $resultado=$conexion->actualizar("tbl_tafi_equipos", $datos,"codigo_equipo='".$_POST["codigo_equipo"]."'");

    if($resultado){
        echo "Equipo Editado";
    }else{
        echo "Error al actualizar el nombre del equipo";
    }
?>