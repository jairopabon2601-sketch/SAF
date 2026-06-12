<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();
    
    $retorno=array();
    
    //EQUIPOS DEL TORNEO
    $sql="SELECT *
    
    FROM tbl_tafi_torneos_calendario_fechas_grupos t 
   
    WHERE t.codigo_torneo='".$_POST["codigo_torneo"]."'
    
    GROUP BY t.nombre_grupo";

    $resultado=$conexion->ejecutar_sql($sql);

    $datos=$resultado->fetch_all(MYSQLI_ASSOC);

    echo json_encode($datos);
?>