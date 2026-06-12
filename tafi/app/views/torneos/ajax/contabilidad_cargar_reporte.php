<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
        
    $conexion=new conexion_db();
    $retorno=array();

    $codigo_torneo=$_POST["codigo_torneo"];

    $sql="SELECT 

    con.codigo_conceptos,
    con.conceptos, 
    TRUNCATE(c.valor,0) AS costo, 
    TRUNCATE(((c.valor * por.porcentaje_torneo) / 100 ),0) AS total_porcentaje_torneo,
    TRUNCATE(((c.valor * por.porcentaje_tafi) / 100 ),0) AS total_porcentaje_tafi, 
    porcentaje_torneo, 
    porcentaje_tafi, 
    porcentaje_interno_torneo
    
    FROM tbl_tafi_torneos_costos c 
    
    INNER JOIN tbl_tafi_torneos_costos_conceptos con ON 
    c.codigo_concepto=con.codigo_conceptos
    
    INNER JOIN tbl_tafi_torneos_costos_porcentajes por ON 
    c.codigo_torneo=por.codigo_torneo
    
    WHERE c.codigo_torneo='".$codigo_torneo."'
    
    GROUP BY c.codigo";

    $resultado=$conexion->ejecutar_sql($sql);

    if($resultado->num_rows>0){
        $retorno["resultado"]=1;

        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
        $retorno["datos_porcentajes"]=$datos;



        
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargaron los porcentajes";
    }
    
    echo json_encode($retorno);

?>