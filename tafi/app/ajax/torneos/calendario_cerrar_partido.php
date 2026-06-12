<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();
    //SE ACTUALIZA EL ESTADO DEL PARTIDO Y EL RESULTADO

    $retorno=array();
    $codigo_calendario=$_POST["codigo_calendario"];
    $codigo_grupo=0;

    //SE CONSULTA EL ESTADO DEL PARTIDO
    $sql="SELECT codigo_estado, codigo_grupo, codigo_torneo FROM tbl_tafi_torneos_calendario WHERE codigo_calendario=".$codigo_calendario;
    $resultado=$conexion->ejecutar_sql($sql);

    if($resultado){
        
        $datos=$resultado->fetch_all(MYSQLI_ASSOC);

        $codigo_estado=$datos[0]["codigo_estado"];
        $codigo_grupo=$datos[0]["codigo_grupo"];
        $codigo_torneo=$datos[0]["codigo_torneo"];

        //ESTADO EN CURSO PARA PODER CERRA EL PARTIDO
        if($codigo_estado==2){

            $datos=array();
            $datos["codigo_estado"]=3;
            $datos["resultado_local"]=$_POST["resultado_local"];
            $datos["resultado_visitante"]=$_POST["resultado_visitante"];

            $resultado=$conexion->actualizar("tbl_tafi_torneos_calendario", $datos, "codigo_calendario=".$codigo_calendario);

            if($resultado){

                $resultado_jugadores="";

                //SE GRABA EL RESULTADO DEL EQUIPO LOCAL
                $l=0;
                foreach($_POST["local_jugador"] AS $codigo_jugador){
                    $datos=array();
                    $datos["codigo_calendario"]=$codigo_calendario;
                    $datos["codigo_jugador"]=$codigo_jugador;
                    $datos["codigo_equipo"]=$_POST["codigo_equipo_local"];
                    $datos["numero_amarillas"]=$_POST["local_amarillas"][$l];
                    $datos["numero_rojas"]=$_POST["local_rojas"][$l];
                    $datos["numero_goles"]=$_POST["local_goles"][$l];

                    if(in_array($codigo_jugador, $_POST['local_titular'])){
                        $datos["titular"]=1;
                    }else{
                        $datos["titular"]=0;
                    }
                    

                    $resultado=$conexion->insertar("tbl_tafi_torneos_calendario_resultados", $datos);

                    if(!$resultado){
                        $resultado_jugadores.="No se pudo actualizar el resultado del jugador ".$codigo_jugador ."-".$conexion->error();
                    }
                    $l++;
                }

                //SE GRABA EL RESULTADO DEL EQUIPO VISITANTE
                $v=0;
                foreach($_POST["visitante_jugador"] AS $codigo_jugador){
                    
                    $datos=array();
                    $datos["codigo_calendario"]=$codigo_calendario;
                    $datos["codigo_jugador"]=$codigo_jugador;
                    $datos["codigo_equipo"]=$_POST["codigo_equipo_visitante"];           
                    $datos["numero_amarillas"]=$_POST["visitante_amarillas"][$v];
                    $datos["numero_rojas"]=$_POST["visitante_rojas"][$v];
                    $datos["numero_goles"]=$_POST["visitante_goles"][$v];

                    if(in_array($codigo_jugador, $_POST['visitante_titular'])){
                        $datos["titular"]=1;
                    }

                    $resultado=$conexion->insertar("tbl_tafi_torneos_calendario_resultados", $datos);

                    if(!$resultado){
                        $resultado_jugadores.="No se pudo actualizar el resultado del jugador ".$codigo_jugador ."-".$conexion->error();
                    }
                    $v++;
                }

                if($resultado_jugadores==""){
                    $retorno["mensaje"]="Resultados registrados correctamente";
                    $retorno["resultado"]=1;
                }else{
                    $retorno["error"]="Error al registrar los resultados ".$resultado_jugadores;
                    $retorno["resultado"]=0;
                }

                if($codigo_grupo>0){

                    //SE VALIDA SI ES EL ULTIMO PARTIDO DE LOS GRUPOS PARA PASAR A LA SIGUIENTE FASE
                    
                    /*$sql="SELECT COUNT(*) AS cantidad FROM tbl_tafi_torneos_calendario WHERE codigo_torneo=".$codigo_torneo." AND codigo_estado IN (1,2)";
                    $resultado=$conexion->ejecutar_sql($sql);
                    $datos=$resultado->fetch_all(MYSQLI_ASSOC);

                    $retorno["partidos_restantes"]=$datos[0]["cantidad"];*/

                }
                
            }else{
                $retorno["error"]="No se pudo actualizar el calendario ".$codigo_calendario ."-".$conexion->error();
                $retorno["resultado"]=0;
            }
        }else{
            $retorno["error"]="El partido no se encuentra en estado en curso, debe habilitar el partido para poder cerrarlo.";
            $retorno["resultado"]=0;
        }
    }else{
        $retorno["error"]="No se pudo consultar el calendario ".$codigo_calendario ."-".$conexion->error();
        $retorno["resultado"]=0;
    }
    
    $retorno["codigo_grupo"]=$codigo_grupo;

    echo json_encode($retorno);

?>