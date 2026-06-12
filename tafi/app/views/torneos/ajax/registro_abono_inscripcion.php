<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    
$conexion=new conexion_db();
$retorno="";

$codigo_torneo_equipo=$_POST["codigo_torneo_equipo"];
$valor_abono=$_POST["valor_abono"];
$fecha_pago=$_POST["fecha_pago"];

$sql="SELECT 

te.codigo_torneo, 
te.codigo_equipo,
truncate(tc.valor,0) AS valor_inscripcion, 
SUM(abono.valor_abono) AS valor_pagado

FROM  tbl_tafi_torneos_equipos te 

left JOIN tbl_tafi_torneos_costos tc ON 
te.codigo_torneo=tc.codigo_torneo
AND tc.codigo_concepto=1

LEFT JOIN tbl_tafi_torneos_inscripcion_abonos abono ON 
te.codigo_torneo=abono.codigo_torneo
AND te.codigo_equipo=abono.codigo_equipo

WHERE te.codigo_torneo_equipo='".$codigo_torneo_equipo."'";

$resultado=$conexion->ejecutar_sql($sql);

if($resultado->num_rows>0){
    $datos=$resultado->fetch_all(MYSQLI_ASSOC);

    $valor_inscripcion=$datos[0]["valor_inscripcion"];
    $valor_pagado=$datos[0]["valor_pagado"];
    $codigo_torneo=$datos[0]["codigo_torneo"];
    $codigo_equipo=$datos[0]["codigo_equipo"];


    if( ($valor_pagado+$valor_abono) > $valor_inscripcion){
        $retorno="El valor abonado supera el valor de la inscripcion";
    }else{

        $datos=array();
        $datos["codigo_torneo"]=$codigo_torneo;
        $datos["codigo_equipo"]=$codigo_equipo;
        $datos["valor_abono"]=$valor_abono;
        $datos["fecha_abondo"]=$fecha_pago;

        $resultado=$conexion->insertar("tbl_tafi_torneos_inscripcion_abonos", $datos);

        if($resultado){
            $retorno="Abono Registrado";
        }else{
            $retorno="Error al registara abono";
        }
    }
    
}else{
    $retorno="No se cargaron los datos";
}

echo $retorno;

?>