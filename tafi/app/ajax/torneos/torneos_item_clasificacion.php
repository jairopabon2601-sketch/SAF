<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();  

    $sql="SELECT 

    it.codigo_item_clasificacion,
    it.item, 
    it.campo_orden
    
    FROM tbl_tafi_torneos_tipos_clasificacion_items it";

    $resultado=$conexion->ejecutar_sql($sql);
    $datos=$resultado->fetch_all(MYSQLI_ASSOC);

    echo json_encode($datos);
?>