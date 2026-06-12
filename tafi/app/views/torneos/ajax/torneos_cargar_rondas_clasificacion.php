<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    
$conexion=new conexion_db();
$retorno=array();

$sql="SELECT 

ronda.codigo_ronda,
ronda.numero

FROM tbl_tafi_torneos_calendario_fechas fech 

INNER JOIN tbl_tafi_torneos_calendario_fechas_rondas ronda on 
fech.codigo_ronda=ronda.codigo_ronda

WHERE fech.codigo_torneo='".$_POST["codigo_torneo"]."'

GROUP BY ronda.numero";

$resultado=$conexion->ejecutar_sql($sql);

if($resultado->num_rows>0){
    $retorno["resultado"]=1;
    
    $datos=$resultado->fetch_all(MYSQLI_ASSOC);
    $retorno["datos"]=$datos;
}else{
    $retorno["resultado"]=0;
}

echo json_encode($retorno);
?>