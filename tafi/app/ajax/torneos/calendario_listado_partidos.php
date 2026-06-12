<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();


    $sql="SELECT 

    MD5(CONCAT('FECHA',fechas.codigo_calendario)) AS llave,
    if(fechas.fecha IS NULL OR fechas.hora IS NULL OR (fechas.codigo_sede IS NULL OR  fechas.codigo_sede=0),0,1) AS permite_registro,
    fechas.codigo_calendario,
    fechas.fecha, 
    fechas.hora,
    sed.codigo_sede, 
    if(fechas.codigo_sede IS NULL OR  fechas.codigo_sede=0,'No Definido',sed.nombre)  as sede, 
    elocal.codigo_equipo AS codigo_local,
    elocal.nombre_equipo AS equipo_local,
    elocal.escudo AS escudo_local,
    fechas.resultado_local,
    evisitante.codigo_equipo AS codigo_visitante,
    evisitante.nombre_equipo AS equipo_visitante, 
    evisitante.escudo AS escudo_visitante,
    fechas.resultado_visitante,
    esta.estado
    
    FROM tbl_tafi_torneos_calendario fechas
    
    INNER JOIN  tbl_tafi_equipos elocal ON 
    fechas.codigo_local=elocal.codigo_equipo
    
    INNER JOIN  tbl_tafi_equipos evisitante ON 
    fechas.codigo_visitante=evisitante.codigo_equipo

    INNER JOIN tbl_tafi_torneos_calendario_estados esta ON
    fechas.codigo_estado=esta.codigo_estado
    
    LEFT JOIN tbl_tafi_torneos_sedes sed ON
    fechas.codigo_sede=sed.codigo_sede
    
    WHERE fechas.codigo_torneo='".$_POST["codigo_torneo"]."'
    AND fechas.codigo_fecha='".$_POST["codigo_fecha"]."'";
    
    if(codigo_clasificacion==2){
        $sql.=" AND fechas.codigo_grupo='".$_POST["codigo_grupo"]."' ";
    }

    $sql.="ORDER BY fechas.codigo_fecha";

    $resultado=$conexion->ejecutar_sql($sql);

    $datos=$resultado->fetch_all(MYSQLI_ASSOC);
    echo json_encode($datos);

?>