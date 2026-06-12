<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/docs.php");

    $retorno="";
    $retorno.= validar_peso_archivo("logo");

    if($retorno==""){
        $retorno.= validar_formato("logo","png");

        if($retorno==""){
           

            $conexion=new conexion_db();    

            $datos=array();
            $datos["codigo_tipo_torneo"]=1;
            $datos["nombre_equipo"]=$_POST["nombre_equipo"];
            $datos["codigo_delegado"]=$_SESSION["codigo_origen"];
        
            $resultado=$conexion->insertar("tbl_tafi_equipos", $datos);

            if($resultado){
                $codigo_equipo=$conexion->insert_id();
                
                $retorno=cargar_archivo("logo","logo_".$codigo_equipo, "../archivos/equipos/escudos/");
                
                if ($retorno=="") {
                    $datos=array();
                    $datos["escudo"]="logo_".$codigo_equipo.".png";
                
                    $resultado=$conexion->actualizar("tbl_tafi_equipos", $datos,"codigo_equipo=".$codigo_equipo);

                    if($resultado){

                        if($_SESSION["administrador"]==0){
                            $datos=array();
                            $datos["permite_crear_equipo"]=0;

                            $conexion->actualizar("tbl_tafi_delegados", $datos,"codigo_delegado=".$_SESSION["codigo_origen"]);
                        }    

                        $retorno="Se ha registrado el equipo correctamente.";
                    }else{
                        $retorno="Error al actualizar el logo del equipo.";
                    }
                }

            }else{
                $retorno="Error al crear el equipo";
            }
        
        }
    }

    //redireccionar con un alert
    echo "<script language='javascript'>alert('".$retorno."');window.location.href='../dashboard.php?proc=8';</script>";
?>