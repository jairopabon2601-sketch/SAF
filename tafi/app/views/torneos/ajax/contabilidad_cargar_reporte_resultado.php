<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
        
    $conexion=new conexion_db();
    $retorno=array();

    $codigo_ronda=$_POST["codigo_ronda"];

    $sql="SELECT 

    cal.codigo_calendario,
    eq_l.codigo_equipo AS codlocal, 
    eq_v.codigo_equipo AS codvisitante,
    eq_l.nombre_equipo AS elocal, 
    eq_v.nombre_equipo AS evisitante, 
    cal.fecha
     
    FROM tbl_tafi_torneos_calendario_fechas_rondas r 
    
    INNER JOIN tbl_tafi_torneos_calendario_fechas fech ON 
    r.codigo_ronda=fech.codigo_ronda
    
    INNER JOIN tbl_tafi_torneos_calendario cal ON 
    fech.codigo_fecha=cal.codigo_fecha
    
    INNER JOIN tbl_tafi_equipos eq_l ON 
    cal.codigo_local=eq_l.codigo_equipo
    
    INNER JOIN tbl_tafi_equipos eq_v ON 
    cal.codigo_visitante=eq_v.codigo_equipo
    
    WHERE r.codigo_ronda='".$codigo_ronda."'
    AND cal.codigo_estado=3
    
    GROUP BY cal.codigo_calendario
    
    ORDER BY cal.fecha DESC";

    $resultado=$conexion->ejecutar_sql($sql);

    
    if($resultado->num_rows>0){
        $retorno["resultado"]=1;

        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
        $retorno["datos_equipos"]=$datos;

        //SE CONSULTAN LAS AMARILLAS 
        $consultas=$conexion->buscar("tbl_tafi_torneos_costos_conceptos","codigo_conceptos in (2,3,4)");
        $sql_amarillas="";
        $sql_rojas="";
        $sql_arbitraje="";

        foreach($consultas as $consulta){
            switch ($consulta["codigo_conceptos"]) {
                case 2:
                    $sql_arbitraje=$consulta["consulta"];
                    break;
                case 3:
                    $sql_amarillas= $consulta["consulta"];
                    break;
                case 4:
                    $sql_rojas=$consulta["consulta"];
                    break;
            }
        }


        $amarillas=[];
        $rojas=[];
        $arbitraje=[];

        $x=0;
        foreach($datos as $dato){

            for($i=0; $i<=1;$i++){

                $codigo_equipo=0;

                if($i==0){
                    $filtro="r.codigo_calendario='".$dato["codigo_calendario"]."' AND r.codigo_equipo='".$dato["codlocal"]."'";
                    $codigo_equipo=$dato["codlocal"];

                    $filtro_arbitraje="cal.codigo_calendario='".$dato["codigo_calendario"]."' AND cal.codigo_local='".$dato["codlocal"]."'";
                    $filtro_arbitraje.=" and cal.codigo_calendario=abono.codigo_calendario AND cal.codigo_local=abono.codigo_equipo";

                    $campo_equipo="cal.codigo_local";
                }else{
                    $filtro="r.codigo_calendario='".$dato["codigo_calendario"]."' AND r.codigo_equipo='".$dato["codvisitante"]."'";
                    $codigo_equipo=$dato["codvisitante"];

                    $filtro_arbitraje="cal.codigo_calendario='".$dato["codigo_calendario"]."' AND cal.codigo_visitante='".$dato["codvisitante"]."'";
                    $filtro_arbitraje.=" and cal.codigo_calendario=abono.codigo_calendario AND cal.codigo_visitante=abono.codigo_equipo";

                    $campo_equipo="cal.codigo_visitante";
                }

                //CONSULTA DE ARBITRAJE
                //SE REEMPLAZA EL CAMPO <<campo_equipo>> POR EL CAMPO QUE CORRESPONDA
                $sql=str_replace("<<campo_equipo>>",$campo_equipo,$sql_arbitraje);
                $sql=str_replace("<<filtro>>",$filtro_arbitraje,$sql);

                $resultado=$conexion->ejecutar_sql($sql);
                if($resultado->num_rows>0){
                    $datos=$resultado->fetch_all(MYSQLI_ASSOC);
                    $arbitraje[]=$datos[0];
                }
                
                //CONSULTA DE AMARIILLAS
                $sql=str_replace("<<filtro>>",$filtro,$sql_amarillas);
                $resultado=$conexion->ejecutar_sql($sql);

                if($resultado->num_rows>0){
                    $datos=$resultado->fetch_all(MYSQLI_ASSOC);
                    $amarillas[]=$datos[0];
                }

                //CONSULTA DE ROJAS
                $sql=str_replace("<<filtro>>",$filtro,$sql_rojas);
                $resultado=$conexion->ejecutar_sql($sql);

                if($resultado->num_rows>0){
                    $datos=$resultado->fetch_all(MYSQLI_ASSOC);
                    $rojas[]=$datos[0];
                }
            } 


            $x++;
        }

        $retorno["datos_contabilidad_amarillas"]=$amarillas;
        $retorno["datos_contabilidad_rojas"]=$rojas;
        $retorno["datos_contabilidad_arbitraje"]=$arbitraje;


    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargaron los equipos";
    }
    
    echo json_encode($retorno);



?>