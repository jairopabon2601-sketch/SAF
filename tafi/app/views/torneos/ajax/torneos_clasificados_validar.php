<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/consultar_equipos_clasificados.php");

        
    $conexion=new conexion_db();
    $retorno=array();
    $codigo_torneo = $_POST["codigo_torneo"];

    $sql="SELECT 
    COUNT(*) as partidos_restantes, 
    t.codigo_responsable,
    r.codigo_ronda,
    r.numero as ronda, 
    r.codigo_clasificacion,
    r.cantidad_equipos_clasifican

    FROM tbl_tafi_torneos_calendario  c
    
    INNER JOIN tbl_tafi_torneos_calendario_fechas f ON 
    c.codigo_fecha=f.codigo_fecha

    INNER JOIN tbl_tafi_torneos_calendario_fechas_rondas r ON
    f.codigo_ronda=r.codigo_ronda

    INNER JOIN tbl_tafi_torneos t ON
    c.codigo_torneo=t.codigo_torneo

    WHERE c.codigo_torneo='".$codigo_torneo."'
    AND c.codigo_estado!=3
    AND f.codigo_ronda=(SELECT fe.codigo_ronda 
    FROM tbl_tafi_torneos_calendario_fechas_rondas fe
    WHERE fe.codigo_torneo='".$codigo_torneo."'
    GROUP BY fe.numero
    ORDER BY fe.numero DESC LIMIT 1)";

    $resultado=$conexion->ejecutar_sql($sql);
    
    if($resultado->num_rows>0){
        
        $datos_partidos_restantes=$resultado->fetch_all(MYSQLI_ASSOC);
        $partidos_restantes = $datos_partidos_restantes[0]["partidos_restantes"];
        $codigo_responsable = $datos_partidos_restantes[0]["codigo_responsable"];
        $cantidad_equipos_clasifican = $datos_partidos_restantes[0]["cantidad_equipos_clasifican"];
        $codigo_ronda=$datos_partidos_restantes[0]["codigo_ronda"];
        $codigo_clasificacion=$datos_partidos_restantes[0]["codigo_clasificacion"];

     
        if($codigo_responsable==$_SESSION["codigo_origen"] ){

            if($partidos_restantes==0){
                $retorno["resultado"]=1;
                $retorno["partidos_restantes"]=$partidos_restantes;
                $retorno["proxima_ronda"]=$datos_partidos_restantes[0]["ronda"]+1;

                //SE CONSULTA LOS EQUIPOS CLASIFICADOS
                $clasificados=consultar_equipos_clasificados($codigo_torneo, $cantidad_equipos_clasifican, $codigo_ronda, $codigo_clasificacion);

                $retorno["equipos_clasificados"]=$clasificados["datos"];
            }else{
                $retorno["resultado"]=0;
                $retorno["mensaje"]="Aun hay partidos restantes";
            }
        }else{
            $retorno["resultado"]=0;
            $retorno["mensaje"]="Acceso denegado";

        }
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="no hay partidos restantes";
    }

    echo json_encode($retorno);
?>