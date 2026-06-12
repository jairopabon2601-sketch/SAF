<?php
    session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();    
    $retorno=array();

    $sql="SELECT 

    pos.nombre AS 'posicion',
    ju.codigo_jugador,
    ju.numero_documento,
    UPPER(CONCAT(ju.nombres,' ',ju.apellidos)) AS jugador,
    ju.fecha_nacimiento, 
    ju.eps, 
    ju.ruta_foto,
    je.dorsal
    
    FROM tbl_tafi_jugadores ju 
    
    INNER JOIN tbl_tafi_equipos_jugadores je ON 
    ju.codigo_jugador=je.codigo_jugador
    
    INNER JOIN tbl_tafi_jugadores_posicion pos ON 
    je.codigo_posicion=pos.codigo_posicion
    
    WHERE je.codigo_equipo='".$_POST["codigo_equipo"]."'
    AND je.activo=1
    
    ORDER BY pos.orden";
    
    $resultado=$conexion->ejecutar_sql($sql);

   if($resultado->num_rows>0){

        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
         
        $retorno["resultado"]=1;
        $retorno["datos"]= $datos;
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargaron los jugadores";
    }

    echo json_encode($retorno);    

   
?>