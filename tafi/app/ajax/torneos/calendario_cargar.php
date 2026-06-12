<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/calendarios.php");

    $conexion=new conexion_db();
    $calendario=new calendario();

    $retorno=array();
    
    //EQUIPOS DEL TORNEO
    $sql="SELECT 

    t.codigo_torneo,
    t.cantidad_rondas, 
    t.codigo_clasificacion,
    te.codigo_equipo,
    te.codigo_equipo, 
    eq.nombre_equipo, 
    eq.escudo,
    t.calendario_registrado
    
    FROM tbl_tafi_torneos t 
    
    INNER JOIN tbl_tafi_torneos_equipos te ON 
    t.codigo_torneo=te.codigo_torneo
    
    INNER JOIN tbl_tafi_equipos eq ON 
    te.codigo_equipo=eq.codigo_equipo
    
    WHERE t.codigo_torneo='".$_POST["codigo_torneo"]."'
    
    GROUP BY eq.codigo_equipo";

    $resultado=$conexion->ejecutar_sql($sql);

      
    if($resultado->num_rows>0){
        $retorno["resultado"]=1;
        
        $datos=$resultado->fetch_all(MYSQLI_ASSOC);


        //datos de la ronda 1 
        $calendario_registrado=$datos[0]["calendario_registrado"];
        $codigo_clasificacion=$datos[0]["codigo_clasificacion"];   
        $cantidad_rondas=$datos[0]["cantidad_rondas"];
        $codigo_torneo=$datos[0]["codigo_torneo"];
        $codigo_ronda=$_POST["codigo_ronda"];  

        //SE CONSULTAN LOS DATOS DEL CALENDARIO REGISTRADO 
        $sql="SELECT 
        numero, 
        codigo_clasificacion, 
        calendario_registrado
        FROM tbl_tafi_torneos_calendario_fechas_rondas 
        WHERE codigo_torneo='".$_POST["codigo_torneo"]."'
        AND codigo_ronda='".$codigo_ronda."'
        ORDER BY numero DESC LIMIT 1";
        $resultado=$conexion->ejecutar_sql($sql);
        
        if($resultado->num_rows>0){
            $datos=$resultado->fetch_all(MYSQLI_ASSOC);
            if($datos[0]["numero"]>1){
                $calendario_registrado=$datos[0]["calendario_registrado"];
                $codigo_clasificacion=$datos[0]["codigo_clasificacion"];
            }
        }

        $equipos=array();
        $datos_equipo=array();
        $fechas=array();
        $grupos=array();

        $retorno["cantidad_rondas"]=$cantidad_rondas;
        $retorno["codigo_clasificacion"]=$codigo_clasificacion;
        $retorno["calendario_registrado"]=$calendario_registrado;


        if($calendario_registrado==0){
            //SE CARGA EL CALENDARIO POR PRIMERA VEZ
            foreach ($datos as $key => $value) {
                $equipos[]=$value["codigo_equipo"];
                $datos_equipo[$value["codigo_equipo"]]=array("nombre_equipo"=>$value["nombre_equipo"], "escudo"=>$value["escudo"], "codigo_equipo"=>$value["codigo_equipo"]);
            }

            if($codigo_clasificacion==1){
                $partidos=$calendario->todosContraTodos($equipos, $cantidad_rondas);

                foreach ($partidos as $key => $value) {

                 
                    foreach ($value as $key2 => $value2) {

                        $value2=explode("vs", $value2);
                        $fechas[$key][$key2][0]=$datos_equipo[$value2[0]];
                        $fechas[$key][$key2][1]=$datos_equipo[$value2[1]];
                    }
                }

            }else{

                $equipos_grupos=$_POST["cantidad_equipos_grupos"];
                $resultado=$calendario->faseGrupo($equipos,$equipos_grupos,$cantidad_rondas);

                //SE BUSCA EL AREGLO DE LOS EQUIPOS
                $i=0;
                foreach ( $resultado["grupos"] as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $grupos[$i][$key2]=$datos_equipo[$value2];
                    }
                    $i++;   
                }

                //SE BUSCA EL AREGLO DE LOS PARTIDOS
                $i=0;
                foreach ( $resultado["partidos"] as $key => $value) {
                   
                    foreach ($value as $key2 => $value2) {

                        foreach ($value2 as $key3 => $value3) {
                            $value2=explode("vs", $value3);

                            $fechas[$key][$key2][$key3][0]=$datos_equipo[$value2[0]];
                            $fechas[$key][$key2][$key3][1]=$datos_equipo[$value2[1]];
                        }
                  
                    }
                    $i++;   
                }
               
            }

            $retorno["partidos"]=$fechas;
            $retorno["grupos"]=$grupos;

        }else{
            //SE CARGA EL CALENDARIO REGISTRADO
            $datos_calenario=$calendario->CargarCalendario($codigo_torneo,$codigo_clasificacion,$codigo_ronda);

            $cod_grupo=0;
            $grupos_letras;
            $x=0;
            foreach ($datos_calenario["grupos"] as $key => $value) {
                
                if($cod_grupo!=$value["codigo_grupo"]){
                    $grupos_letras[$x]["codigo_grupo"]=$value["codigo_grupo"];
                    $grupos_letras[$x]["nombre_grupo"]=$value["nombre_grupo"];
                    $cod_grupo=$value["codigo_grupo"];
                    $x++;
                }
                
            }

            //SE VALIDA QUE ESTEN REGISTRADOS TODOS LOS PARTIDOS DE LA RONDA DE CONSULTA 
            $sql="SELECT 
            COUNT(*) as partidos_restantes
            FROM tbl_tafi_torneos_calendario  c
            
            INNER JOIN tbl_tafi_torneos_calendario_fechas f ON 
            c.codigo_fecha=f.codigo_fecha
            
            WHERE c.codigo_torneo='".$codigo_torneo."'
            and f.codigo_ronda='".$codigo_ronda."'
            AND c.codigo_estado!=3";

            $resultado=$conexion->ejecutar_sql($sql);
            $datos_partidos_restantes=$resultado->fetch_all(MYSQLI_ASSOC);
            $retorno["partidos_restantes_ronda_consulta"]=$datos_partidos_restantes[0]["partidos_restantes"];

            //SE VALIDA QUE ESTEN REGISTRADOS TODOS LOS PARTIDOS DE LA ULTIMA RONDA
            $sql="SELECT 

            COUNT(*) as partidos_restantes, 
            ron.cantidad_equipos_clasifican

            
            FROM tbl_tafi_torneos_calendario  c
            
            INNER JOIN tbl_tafi_torneos_calendario_fechas f ON 
            c.codigo_fecha=f.codigo_fecha

            INNER JOIN tbl_tafi_torneos_calendario_fechas_rondas ron ON
            f.codigo_ronda=ron.codigo_ronda
            
            WHERE c.codigo_torneo='".$codigo_torneo."'
            AND c.codigo_estado!=3
            AND f.codigo_ronda=(SELECT 
            ron.codigo_ronda 
            FROM tbl_tafi_torneos_calendario_fechas_rondas ron
            
            WHERE ron.codigo_torneo='".$codigo_torneo."'
            GROUP BY ron.numero
            ORDER BY ron.numero DESC LIMIT 1)";

            $resultado=$conexion->ejecutar_sql($sql);
            $datos_partidos_restantes=$resultado->fetch_all(MYSQLI_ASSOC);

            $retorno["partidos_restantes"]=$datos_partidos_restantes[0]["partidos_restantes"];
            $retorno["cantidad_equipos_clasifican"]=$datos_partidos_restantes[0]["cantidad_equipos_clasifican"];

            $retorno["partidos"]=$datos_calenario["fechas"];
            $retorno["grupos"]=$datos_calenario["grupos"];
            $retorno["grupos_letras"]=$grupos_letras;

        }

    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No hay equipos registrados";
    }


    echo json_encode($retorno);
    
?>