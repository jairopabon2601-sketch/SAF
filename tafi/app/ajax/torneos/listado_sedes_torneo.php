<?php
     session_start();
     require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
 
     $conexion=new conexion_db();    
     $retorno=array();
    
    $sql="select * from tbl_tafi_torneos_sedes where codigo_torneo='".$_POST["codigo_torneo"]."'";
    $resultado=$conexion->ejecutar_sql($sql);

    $datos=$resultado->fetch_all(MYSQLI_ASSOC);

    echo json_encode($datos);

?>