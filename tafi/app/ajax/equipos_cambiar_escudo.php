<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/docs.php");

    $conexion=new conexion_db();    
    $retorno=array();


    $resultado="";
    $codigo_equipo=$_POST["codigo_equipo_escudo"];

    $resultado.= validar_peso_archivo("logo");

    if($resultado==""){

        $tipo_archivo_foto= $_FILES['logo']['type'];
        $tipo_archivo_foto=explode("/",$tipo_archivo_foto);  
        
        $resultado=cargar_archivo("logo","equipo_".$codigo_equipo, "../archivos/equipos/escudos/");

        if($resultado==""){

            $datos=array();
            $datos["escudo"]="equipo_".$codigo_equipo.".".$tipo_archivo_foto[1];

            $resultado=$conexion->actualizar("tbl_tafi_equipos", $datos,"codigo_equipo=".$codigo_equipo);

            if($resultado){
                $resultado="Escudo actualizado correctamente";
            }else{
                $resultado="Ocurrió algún error al subir el fichero. No pudo guardarse.";
            }

        }else{
            $resultado="Ocurrió algún error al subir el fichero. No pudo guardarse.";
        }
        
    }else{
        $resultado="El archivo no cumple con las condiciones de peso. 3MB máximo.";
    }

    //redireccionar con un alert
    echo "<script>alert('".$resultado."'); window.location.href='../dashboard.php?proc=9&codigo_equipo=".$codigo_equipo."';</script>";
?>