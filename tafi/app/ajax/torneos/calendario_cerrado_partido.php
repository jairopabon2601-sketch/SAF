<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
 
    $conexion=new conexion_db();

    $codigo_calendario=$_POST["codigo_calendario"];

    $datos_local=array();
    $datos_visitante=array();

     //EQUIPOS 
    $sql="SELECT * FROM tbl_tafi_torneos_calendario cal WHERE codigo_calendario=".$codigo_calendario;
    $resultado=$conexion->ejecutar_sql($sql);

    if($resultado){
        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
        $codigo_local=$datos[0]["codigo_local"];
        $codigo_visitante=$datos[0]["codigo_visitante"];

        $sql="SELECT 

            '0' as 'tipo',
            e.codigo_jugador,
            e.dorsal,
            CONCAT(jug.nombres,' ',jug.apellidos) AS jugador,
            if(res.titular=1,'Si','No') AS titular,
            res.numero_amarillas,
            res.numero_rojas, 
            res.numero_goles
             
            FROM tbl_tafi_equipos_jugadores e 
            
            INNER JOIN tbl_tafi_jugadores jug ON 
            e.codigo_jugador=jug.codigo_jugador
            
            INNER JOIN tbl_tafi_torneos_calendario_resultados res ON 
            res.codigo_calendario='".$codigo_calendario."'
            AND res.codigo_jugador=jug.codigo_jugador
            AND res.codigo_equipo=e.codigo_equipo
            
            WHERE e.codigo_equipo='".$codigo_local."'
            
            UNION ALL 
            
            SELECT 
            
            '1' as 'tipo',
            e.codigo_jugador,
            e.dorsal,
            CONCAT(jug.nombres,' ',jug.apellidos) AS jugador,
            if(res.titular=1,'Si','No') AS titular,
            res.numero_amarillas,
            res.numero_rojas, 
            res.numero_goles
             
            FROM tbl_tafi_equipos_jugadores e 
            
            INNER JOIN tbl_tafi_jugadores jug ON 
            e.codigo_jugador=jug.codigo_jugador
            
            INNER JOIN tbl_tafi_torneos_calendario_resultados res ON 
            res.codigo_calendario='".$codigo_calendario."'
            AND res.codigo_jugador=jug.codigo_jugador
            AND res.codigo_equipo=e.codigo_equipo
            
            WHERE e.codigo_equipo='".$codigo_visitante."'";

            $resultado=$conexion->ejecutar_sql($sql);

            if($resultado){
                $datos=$resultado->fetch_all(MYSQLI_ASSOC);
                $datos_local=array();
                $datos_visitante=array();

                foreach($datos as $dato){
                    if($dato["tipo"]=="0"){
                        $datos_local[]=$dato;
                    }else{
                        $datos_visitante[]=$dato;
                    }
                }
            }

    }

    $resultado=array();
    $resultado["local"]=$datos_local;
    $resultado["visitante"]=$datos_visitante;

    echo json_encode($resultado);
?>