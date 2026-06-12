<?php
    session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();    
    $retorno=array();

    $sql="SELECT 

    con.codigo_conceptos,
    con.conceptos, 
    format(tc.valor,0) as valor
    
    FROM tbl_tafi_tipo_torneos_costos ttc 
    
    INNER JOIN tbl_tafi_torneos_costos_conceptos con ON 
    ttc.codigo_concepto=con.codigo_conceptos
    
    INNER JOIN tbl_tafi_torneos_costos tc ON 
    tc.codigo_concepto=con.codigo_conceptos
    
    WHERE tc.codigo_torneo='".$_POST["codigo_torneo"]."'
    
    GROUP BY tc.codigo";
    
    $resultado=$conexion->ejecutar_sql($sql);

   if($resultado->num_rows>0){

        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
         
        $retorno["resultado"]=1;
        $retorno["datos"]= $datos;
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargaron los costos";
    }

    echo json_encode($retorno);    

   
?>