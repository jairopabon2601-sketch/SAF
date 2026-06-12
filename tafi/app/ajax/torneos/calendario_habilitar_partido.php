<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();

    $codigo_calendario=$_POST["codigo_calendario"];

    $datos_local=array();
    $datos_visitante=array();

    $datos=array();
    $datos["codigo_estado"]=2;
    $resultado=$conexion->actualizar("tbl_tafi_torneos_calendario", $datos, "codigo_calendario=".$codigo_calendario);

    if($resultado){
        //EQUIPOS 
        $sql="SELECT * FROM tbl_tafi_torneos_calendario cal WHERE codigo_calendario=".$codigo_calendario;
        $resultado=$conexion->ejecutar_sql($sql);

        if($resultado){
            $datos=$resultado->fetch_all(MYSQLI_ASSOC);
            $codigo_local=$datos[0]["codigo_local"];
            $codigo_visitante=$datos[0]["codigo_visitante"];

            //SE BUSCAR LOS JUGADORES del equipo 
            $sql="SELECT 

            '0',
            e.codigo_jugador,
            e.dorsal,
            CONCAT(jug.nombres,' ',jug.apellidos) AS jugador
             
            FROM tbl_tafi_equipos_jugadores e 
            
            INNER JOIN tbl_tafi_jugadores jug ON 
            e.codigo_jugador=jug.codigo_jugador
            
            WHERE e.codigo_equipo='".$codigo_local."'
            AND e.activo=1
            
            UNION ALL 
            
            SELECT 
            
            '1',
            e.codigo_jugador,
            e.dorsal,
            CONCAT(jug.nombres,' ',jug.apellidos) AS jugador
             
            FROM tbl_tafi_equipos_jugadores e 
            
            INNER JOIN tbl_tafi_jugadores jug ON 
            e.codigo_jugador=jug.codigo_jugador
            
            WHERE e.codigo_equipo='".$codigo_visitante."'
            AND e.activo=1";

            $resultado=$conexion->ejecutar_sql($sql);

            if($resultado){
                $datos=$resultado->fetch_all(MYSQLI_ASSOC);
                $datos_local=array();
                $datos_visitante=array();

                foreach($datos as $dato){
                    if($dato["0"]=="0"){
                        $datos_local[]=$dato;
                    }else{
                        $datos_visitante[]=$dato;
                    }
                }

            }
        }
    }

    $resultado=array();
    $resultado["local"]=$datos_local;
    $resultado["visitante"]=$datos_visitante;

    echo json_encode($resultado);
?>