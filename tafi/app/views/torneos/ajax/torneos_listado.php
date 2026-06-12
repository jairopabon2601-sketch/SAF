<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
        
    $conexion=new conexion_db();
    $retorno=array();
    $filtro=$_POST["filtro"];

    if($filtro==""){
        $filtro=" 1 ";
    }

    $sql='SELECT 

    t.codigo_torneo, 
    t.codigo_torneo AS "Cod.",
    upper(t.nombre_torneo) AS "Nombre del Torneo",
    cla.nombre AS "Formato",
    t.cantidad_equipos AS "Cant. Equipos", 
    t.cantidad_jugadores_equipos AS "Cant. Jugadores", 
    t.fecha_inicio AS "Fecha de Inicio", 
    t.fecha_final AS "Fecha Final",
    t.fecha_limite_equipos AS "Fecha limite para Equipos",
    t.fecha_limite_jugadores AS "Fecha limite para Jugadores",
    upper(c.nombre) AS "Responsable"


    FROM tbl_tafi_torneos t 

    inner JOIN tbl_tafi_contactos_web c ON 
    t.codigo_responsable=c.codigo 

    INNER JOIN tbl_tafi_torneos_tipos_clasificacion cla ON 
    t.codigo_clasificacion=cla.codigo_clasificacion

    WHERE '.$filtro.'

    ORDER BY t.codigo_torneo DESC ';

     $resultado=$conexion->ejecutar_sql($sql);

    if($resultado->num_rows>0){
        $retorno["resultado"]=1;

        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
        $retorno["datos"]=$datos;
        
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargaron los porcentajes";
    }
    
    echo json_encode($retorno);


?>