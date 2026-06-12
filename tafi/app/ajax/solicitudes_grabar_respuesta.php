<?php
    session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include("../php/usuarios.php");

    $conexion=new conexion_db();    
    $user =new usuarios();

    $datos=array();
    $datos["codigo_estado"]=$_POST["codigo_estado"];
    $datos["usuario_respuesta"]=$_SESSION["codigo_usuario"];
    $datos["fecha_respuesta"]=date("Y-m-d");
    $datos["permite_crear_torneo"]="1";

    
    $resultado=$conexion->actualizar("tbl_tafi_contactos_web", $datos, "codigo=".$_POST["codigo_solicitud"]);
   
    if($resultado){

        if($_POST["codigo_estado"]==2){

            //SE CREAN LOS DATOS PARA EL USUARIO
            $datos_contacto=$conexion->buscar("tbl_tafi_contactos_web","codigo=".$_POST["codigo_solicitud"]);

            if(count($datos_contacto)>0){

                $documento=$datos_contacto[0]["numero_documento"];
                $email=$datos_contacto[0]["email"];

                $data=array();
                $data["codigo_origen"]=$_POST["codigo_solicitud"];
                $data["codigo_tipo_usuario"]=2;
                $data["usuario"]=$documento;
                $data["email"]=$email;

                $user->registrar_usuario($data);

                echo 1;
            }else{
                echo 0;
            }

        }else{
            echo 1;
        }
    }else{
        echo 0;
    }
?>