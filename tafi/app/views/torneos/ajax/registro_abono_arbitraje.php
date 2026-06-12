<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    
$conexion=new conexion_db();
$retorno="";

$codigo_equipo_arbitraje=$_POST["codigo_equipo_arbitraje"];
$codigo_calendario_arbitraje=$_POST["codigo_calendario_arbitraje"];
$codigo_torneo=$_POST["codigo_torneo"];

$valor_abono=$_POST["valor_abono"];
$fecha_pago=$_POST["fecha_pago"];

$sql="SELECT 

truncate(tc.valor,0) AS valor_inscripcion, 
SUM(abono.valor_abono) AS valor_pagado

FROM  tbl_tafi_torneos_costos tc  

LEFT JOIN tbl_tafi_torneos_arbitraje_abonos abono ON 
tc.codigo_torneo=abono.codigo_torneo
AND abono.codigo_equipo='".$codigo_equipo_arbitraje."'
AND abono.codigo_calendario='".$codigo_calendario_arbitraje."'

WHERE tc.codigo_torneo='".$codigo_torneo."'
and tc.codigo_concepto=2";

$resultado=$conexion->ejecutar_sql($sql);

if($resultado->num_rows>0){
    $datos=$resultado->fetch_all(MYSQLI_ASSOC);

    $valor_inscripcion=$datos[0]["valor_inscripcion"];
    $valor_pagado=$datos[0]["valor_pagado"];

    if( ($valor_pagado+$valor_abono) > $valor_inscripcion){
        $retorno="El valor abonado supera el valor de la inscripcion";
    }else{
        $datos=array();
        $datos["codigo_torneo"]=$codigo_torneo;
        $datos["codigo_calendario"]=$codigo_calendario_arbitraje;
        $datos["codigo_equipo"]=$codigo_equipo_arbitraje;
        $datos["valor_abono"]=$valor_abono;
        $datos["fecha_abono"]=$fecha_pago;

        $resultado=$conexion->insertar("tbl_tafi_torneos_arbitraje_abonos", $datos);

        if($resultado){
            $retorno="Abono Registrado";
        }else{
            $retorno="Error al registara abono" . $conexion->error;
        }
    }
    
}else{
    $retorno="No se cargaron los datos";
}

echo $retorno;

?>