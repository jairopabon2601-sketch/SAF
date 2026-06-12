<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();
    $codigo_torneo=$_POST["codigo_torneo"];
    $retorno="";

    if(!isset($_POST["numero_ronda"])){
        //SE ACTUALIZAN LOS DATOS DEL TORNEO 
        $datos=array();
        $datos["fixture"]=$_POST["fixture"];
        $datos["cantidad_equipos_clasifican"]=$_POST["cantidad_equipos"];
        $datos["calendario_registrado"]=1;

        $resultado=$conexion->actualizar("tbl_tafi_torneos", $datos, "codigo_torneo=".$codigo_torneo);

        if(!$resultado){
            $retorno.="No se pudo registrar el calendario ".$conexion->error()."<br>";
        }
    }

    if($retorno==""){
        $datos=array();

        $datos["codigo_torneo"]=$codigo_torneo;
        $datos["codigo_clasificacion"]=$_POST["codigo_clasificacion"];

        if(isset($_POST["numero_ronda"])){
            $datos["numero"]=$_POST["numero_ronda"];
        }else{
            $datos["numero"]=1;
        }
        if($_POST["cantidad_equipos_grupos"] !=""){
            $datos["cantidad_equipos_grupos"]=$_POST["cantidad_equipos_grupos"];
        }
        
        $datos["cantidad_equipos_clasifican"]=$_POST["cantidad_equipos_clasifican"];
        $datos["calendario_registrado"]=1;

        $resultado=$conexion->insertar("tbl_tafi_torneos_calendario_fechas_rondas", $datos);

        if($resultado){

            $codigo_ronda=$conexion->insert_id();
        
            if(!isset($_POST["numero_ronda"])){
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
            }

            $retorno="";
            foreach($_POST["numero_fecha"] as $key => $numero_fecha){
                $nombre_fecha=$_POST["nombre_fecha"][$key];

                $datos=array();
                $datos["codigo_torneo"]=$codigo_torneo;
                $datos["codigo_ronda"]=$codigo_ronda;
                $datos["numero"]=$numero_fecha;               
                $datos["nombre_fecha"]=$nombre_fecha;

                $resultado=$conexion->insertar("tbl_tafi_torneos_calendario_fechas", $datos);

                if($resultado){
                    $codigo_fecha=$conexion->insert_id();
                    
                    foreach($_POST["local_fecha_".$numero_fecha]  as $key2 =>$value ){

                        $codigo_local=$value;
                        $codigo_visitante=$_POST["visitante_fecha_".$numero_fecha][$key2];

                        $fecha=$_POST["fecha_".$numero_fecha."_fecha_".$codigo_local."_".$codigo_visitante];
                        $hora=$_POST["fecha_".$numero_fecha."_hora_".$codigo_local."_".$codigo_visitante];

                        $sede=$_POST["sede_".$numero_fecha."_".$codigo_local."_".$codigo_visitante];

                        $datos=array();
                        $datos["codigo_torneo"]=$codigo_torneo;
                        $datos["codigo_fecha"]=$codigo_fecha;
                        $datos["codigo_local"]=$codigo_local;
                        $datos["codigo_visitante"]=$codigo_visitante;
                        
                        if($fecha!=""){
                            $datos["fecha"]=$fecha;
                        }

                        if($hora!=""){
                            $datos["hora"]=$hora;
                        }

                        if($sede>0){
                            $datos["codigo_sede"]=$sede;
                        }

                        $resultado=$conexion->insertar("tbl_tafi_torneos_calendario", $datos);

                        if(!$resultado){
                            $retorno.="No se pudo registrar el equipo ".$value ."-".$conexion->error()."<br>";
                        }

                    }

                
                }else{
                    $retorno.="No se pudo registrar la fecha ".$nombre_fecha ."-".$conexion->error()."<br>";
                }
            }
        }else{
            $retorno.="No se grabó la ronda" . $conexion->error();
        }
    }
    

    echo $retorno;
?>