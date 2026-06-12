<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
        
    $conexion=new conexion_db();

    $codigo_calendario = $_GET["codigo_calendario"];

    $sql='SELECT 

    e.nombre_equipo AS "local", 
    concat("https://www.safenlinea.com/tafi/app/archivos/equipos/escudos/",e.escudo) AS "escudo_local",
    ev.nombre_equipo AS "visitante",
    concat("https://www.safenlinea.com/tafi/app/archivos/equipos/escudos/",ev.escudo) AS "escudo_visitante",
    DATE_FORMAT(c.fecha,"%Y-%m-%d") AS fecha,
    DATE_FORMAT(c.hora,"%h:%i %p") AS hora,
    sed.nombre AS sede 
    
    FROM tbl_tafi_torneos_calendario c
    
    LEFT JOIN tbl_tafi_torneos_sedes sed ON 
    c.codigo_sede=sed.codigo_sede
    
    INNER JOIN  tbl_tafi_equipos e ON 
    c.codigo_local=e.codigo_equipo
    
    INNER JOIN  tbl_tafi_equipos ev ON 
    c.codigo_visitante=ev.codigo_equipo
    
    WHERE c.codigo_calendario='.$codigo_calendario;

    $resultado=$conexion->ejecutar_sql($sql);
    $datos=$resultado->fetch_all(MYSQLI_ASSOC);

    echo json_encode($datos);
?>