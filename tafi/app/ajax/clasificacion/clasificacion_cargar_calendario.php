<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/calendarios.php");

    $conexion=new conexion_db();
    $calendario=new calendario();

    $retorno=array();

    $equipos=array();
    $datos_equipo=array();
    $fechas=array();
    $grupos=array();

    //VARIABLES 
    $cantidad_rondas=$_POST["cantidad_rondas"];
    $codigo_clasificacion=$_POST["codigo_clasificacion"];

    $retorno["cantidad_rondas"]=$cantidad_rondas;
    $retorno["codigo_clasificacion"]=$codigo_clasificacion;


    //SE CARGA EL CALENDARIO POR PRIMERA VEZ
    $i=0;
    foreach ($_POST["codigo_equipo"] as $key => $value) {
        $equipos[]=$value;
        $datos_equipo[$value]=array("nombre_equipo"=>$_POST["nombre_equipo"][$i], "escudo"=>$_POST["escudo"][$i], "codigo_equipo"=>$value);
        $i++;
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

    echo json_encode($retorno);

?>