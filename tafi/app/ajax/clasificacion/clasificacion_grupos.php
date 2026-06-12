<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/consultar_equipos_clasificados.php");

    $codigo_torneo=$_POST["codigo_torneo"];
    
    $conexion=new conexion_db(); 
    $retorno=array();

    //SE CONSULTAN LAS RONDAS DEL TORNEO
    $sql="SELECT 
    r.cantidad_equipos_clasifican,
    r.codigo_ronda,
    r.codigo_clasificacion,
    r.numero, 
    c.nombre, 
    COUNT(DISTINCT f.codigo_fecha) AS num_fechas, 
    COUNT(DISTINCT cal.codigo_calendario) AS num_partidos, 
    SUM(res.numero_goles) AS numero_goles, 
    SUM(res.numero_amarillas) AS numero_amarillas, 
    SUM(res.numero_rojas) AS numero_rojas
    
    FROM tbl_tafi_torneos_calendario_fechas_rondas r 
    
    INNER JOIN tbl_tafi_torneos_tipos_clasificacion c ON 
    r.codigo_clasificacion=c.codigo_clasificacion
    
    INNER JOIN tbl_tafi_torneos_calendario_fechas f ON 
    r.codigo_ronda=f.codigo_ronda
    AND r.codigo_torneo=f.codigo_torneo
    
    INNER JOIN tbl_tafi_torneos_calendario cal ON 
    f.codigo_fecha=cal.codigo_fecha
    AND r.codigo_torneo=cal.codigo_torneo
    
    INNER JOIN tbl_tafi_torneos_calendario_resultados res ON 
    cal.codigo_calendario=res.codigo_calendario
    
    WHERE r.codigo_torneo='".$codigo_torneo."'
    
    GROUP BY r.codigo_ronda
    
    ORDER BY r.numero";

    $resultado=$conexion->ejecutar_sql($sql);

    if($resultado->num_rows > 0){
        $resultado = $resultado->fetch_all(MYSQLI_ASSOC);

        $retorno["resultado"]=1;
        $retorno["datos_rondas"]=$resultado;

        foreach ($resultado as $key => $value) {
            $codigo_clasificacion=$value["codigo_clasificacion"];

            if($codigo_clasificacion==2){

                //SE CONSULTAN LOS GRUPOS DE LA RONDA
                $sql="SELECT 

                fg.codigo_grupo,
                f.codigo_ronda,
                CONCAT('Grupo ', fg.nombre_grupo) AS grupo 
                
                FROM tbl_tafi_torneos_calendario_fechas f 
                
                INNER JOIN tbl_tafi_torneos_calendario_fechas_grupos fg ON 
                f.codigo_grupo=fg.codigo_grupo
                
                WHERE f.codigo_ronda='".$value["codigo_ronda"]."'
                and f.codigo_torneo='".$codigo_torneo."'
                
                GROUP BY f.codigo_grupo";

                $resultado=$conexion->ejecutar_sql($sql);
                $datos_grupos=$resultado->fetch_all(MYSQLI_ASSOC);
                $retorno["datos_rondas"][$key]["grupos"]=$datos_grupos;

                //SE CONSULTA LA TABLA DE POSICIONES DE LOS GRUPOS

                foreach ($datos_grupos as $key2 => $value2) {
                    $args=array();
                    $args["codigo_torneo"]=$codigo_torneo;
                    $args["codigo_ronda"]=$value["codigo_ronda"];
                    $args["codigo_grupo"]=$value2["codigo_grupo"];

                    $retorno["datos_rondas"][$key]["grupos"][$key2]["posiciones"]=consultar_tabla_posiciones($args);
                }

            }else{
                //SE CONSULTA LA TABLA DE POSICIONES DE LA RONDA
                $args=array();
                $args["codigo_torneo"]=$codigo_torneo;
                $args["codigo_ronda"]=$value["codigo_ronda"];

                $retorno["datos_rondas"][$key]["posiciones"]=consultar_tabla_posiciones($args);
            }
            
        }

    }else{
        $retorno["resultado"]=0;
    }

    echo json_encode($retorno);
?>