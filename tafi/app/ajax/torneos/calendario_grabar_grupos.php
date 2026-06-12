<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();
    $retorno="";

    $codigo_torneo=$_POST["codigo_torneo"];


    $datos=array();
    $datos["fixture"]=$_POST["fixture"];
    $datos["cantidad_equipos_grupos"]=$_POST["cantidad_equipos_grupos"];
    $datos["cantidad_equipos_clasifican"]=$_POST["cantidad_equipos_clasifican"];
    $datos["calendario_registrado"]=1;

    $resultado=$conexion->actualizar("tbl_tafi_torneos", $datos, "codigo_torneo=".$codigo_torneo);

    if( $resultado){
        //SE GUARDA LA FASE DE CLASIFICACION
        $datos=array();

        $datos["codigo_torneo"]=$codigo_torneo;
        $datos["codigo_clasificacion"]=2;

        if(isset($_POST["codigo_fase"])){
            $datos["numero"]=$_POST["numero_ronda"];
        }else{
            $datos["numero"]=1;
        }
        
        $datos["cantidad_equipos_grupos"]=$_POST["cantidad_equipos_grupos"];
        $datos["cantidad_equipos_clasifican"]=$_POST["cantidad_equipos_clasifican"];
        $datos["calendario_registrado"]=1;

        $conexion->insertar("tbl_tafi_torneos_calendario_fechas_fases", $datos);
    

        //SE GUARDE EL ITEM DE CLASIFICACION
        $c=1;
        foreach($_POST["codigo_item_clasificacion"] as $item_c){

            $datos=array();
            $datos["codigo_torneo"]=$codigo_torneo;
            $datos["codigo_item"]=$item_c;
            $datos["orden"]=$c;
    
            $conexion->insertar("tbl_tafi_torneos_clasificacion_items", $datos);
            $c++;
        }

       
        foreach($_POST["nombre_grupo"] as $key =>$value){

            $nombre_grupo=$value;

            $datos=array();
            $datos["nombre_grupo"]=$nombre_grupo;
            $datos["codigo_torneo"]=$codigo_torneo;

            $resultado=$conexion->insertar("tbl_tafi_torneos_calendario_fechas_grupos", $datos);

            if($resultado){
                $codigo_grupo=$conexion->insert_id();

                //SE GUARDAN LOS EQUIPOS DEL GRUPO 
                foreach ($_POST["grupo_".$nombre_grupo."_equipo"] as $key => $value) {
                    $datos=array();
                    $datos["codigo_torneo"]=$codigo_torneo;
                    $datos["codigo_grupo"]=$codigo_grupo;
                    $datos["codigo_equipo"]=$value;
            
                    $resultado=$conexion->insertar("tbl_tafi_torneos_calendario_fechas_grupos_equipos", $datos);

                    if(!$resultado){
                        $retorno.="No se pudo registrar el equipo ".$value ."-".$conexion->error()."<br>";
                    }
                }

                //SE GUARDAN LAS FECHAS
                $eq_fecha=0;
                foreach($_POST["nombre_fecha_grupo_".$nombre_grupo] as  $key =>$value){

                    $numero_fecha=$_POST["numero_fecha_grupo_".$nombre_grupo][$eq_fecha];

                    $datos=array();
                    $datos["codigo_grupo"]=$codigo_grupo;
                    $datos["codigo_torneo"]=$codigo_torneo;
                    $datos["numero"]=$numero_fecha;               
                    $datos["nombre_fecha"]=$value;
            
                    $resultado=$conexion->insertar("tbl_tafi_torneos_calendario_fechas", $datos);

                    if($resultado){
                        $codigo_fecha=$conexion->insert_id();
                        
                        $eq=0;
                        foreach($_POST["local_fecha_".$numero_fecha."_".$nombre_grupo] as $key =>$value ){
                            $equipo_local=$value;
                            $equipo_visitante=$_POST["visitante_fecha_".$numero_fecha."_".$nombre_grupo][$eq];
                            $codigo_sede=$_POST[$nombre_grupo.$numero_fecha."_sede"][$eq];

                            //Se procede a guardar los partidos
                            $datos=array();
                            $datos["codigo_torneo"]=$codigo_torneo;
                            $datos["codigo_fecha"]=$codigo_fecha;
                            $datos["codigo_grupo"]=$codigo_grupo;
                            $datos["codigo_local"]=$equipo_local;
                            $datos["codigo_visitante"]=$equipo_visitante;

                            if($_POST[$nombre_grupo.$numero_fecha."_fecha_".$equipo_local."_".$equipo_visitante]!=""){
                                $datos["fecha"]=$_POST[$nombre_grupo.$numero_fecha."_fecha_".$equipo_local."_".$equipo_visitante];
                            }

                            if($_POST[$nombre_grupo.$numero_fecha."_hora_".$equipo_local."_".$equipo_visitante]!=""){
                                $datos["hora"]=$_POST[$nombre_grupo.$numero_fecha."_hora_".$equipo_local."_".$equipo_visitante];
                            }

                            if($codigo_sede>0){
                                $datos["codigo_sede"]=$codigo_sede;
                            }

                            $resultado=$conexion->insertar("tbl_tafi_torneos_calendario", $datos);

                            if(!$resultado){
                                $retorno.="No se pudo registrar el partido ".$equipo_local." vs ".$equipo_visitante."-".$conexion->error()."<br>";
                            }
                            $eq++;
                        }
                        

                    }else{
                        $retorno.="No se pudo registrar la fecha ".$value ."-".$conexion->error()."<br>";
                    }
                    $eq_fecha++;
                }
            }else{
                $retorno.="No se pudo registrar el grupo ".$nombre_grupo."<br>";
            }

        }
    }else{
        $retorno.="No se pudo registrar el calendario ".$conexion->error()."<br>";
    }

    echo $retorno;
?>