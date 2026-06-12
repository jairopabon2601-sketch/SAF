<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/docs.php");
    include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/usuarios.php");

    $tipo_archivo_doc = $_FILES['doc_identidad']['type'];
    $tipo_archivo_doc=explode("/",$tipo_archivo_doc);  

    $tipo_archivo_foto= $_FILES['foto']['type'];
    $tipo_archivo_foto=explode("/",$tipo_archivo_foto);  


    $res="";  

    $res.= validar_peso_archivo("doc_identidad");
    $res.= validar_formato("doc_identidad","pdf");
     
    if($res==""){
        
        $res.= validar_peso_archivo("foto");
        $res.= validar_formato("foto","png");

        if($res==""){

            $conexion=new conexion_db(); 
            $user =new usuarios();

            $datos=array();
            $datos["tipo_documento"]=$_POST["tipo_documento"];
            $datos["numero_documento"]=$_POST["numero_documento"];
            $datos["nombres"]=$_POST["nombres"];
            $datos["apellidos"]=$_POST["apellidos"];
            $datos["fecha_nacimiento"]=$_POST["fecha_nacimiento"];
            $datos["email"]=$_POST["email"];
            $datos["celular"]=$_POST["celular"];
            $datos["eps"]=$_POST["eps"];
        
            $resultado=$conexion->insertar("tbl_tafi_jugadores", $datos);

            if($resultado){

                $codigo_jugador=$conexion->insert_id();

                $datos=array();
                $datos["codigo_equipo"]=$_POST["codigo_equipo_jugador"];
                $datos["codigo_jugador"]= $codigo_jugador;
                $datos["codigo_posicion"]= $_POST["codigo_posicion"];
                $datos["dorsal"]= $_POST["dorsal"];
                $datos["activo"]= 1;    
        
                $resultado=$conexion->insertar("tbl_tafi_equipos_jugadores", $datos);
                
                if($resultado){

                    $res_archivo=cargar_archivo("doc_identidad","documento_identidad_".$codigo_jugador, "../../archivos/jugadores/documento_identidad/");

                    if($res_archivo==""){
                        $datos=array();
                        $datos["ruta_documento"]="documento_identidad_".$codigo_jugador.".pdf";
                        $conexion->actualizar("tbl_tafi_jugadores", $datos,"codigo_jugador=".$codigo_jugador);
                    }

                    $res_foto=cargar_archivo("foto","foto_".$codigo_jugador, "../../archivos/jugadores/fotos/");

                    if($res_foto==""){
                        $datos=array();
                        $datos["ruta_foto"]="foto_".$codigo_jugador.".".$tipo_archivo_foto[1];
                        $conexion->actualizar("tbl_tafi_jugadores", $datos,"codigo_jugador=".$codigo_jugador);
                    }

                    $data=array();
                    $data["codigo_origen"]=$codigo_jugador;
                    $data["codigo_tipo_usuario"]=4;
                    $data["usuario"]=$_POST["numero_documento"];
                    $data["email"]=$_POST["email"];

                    $user->registrar_usuario($data);

                    $res="Se ha registrado el jugador correctamente.";


                }else{
                    $res.="Error al registrar el jugador con el equipo";
                }

            }else{
                $res.="Error al registrar el jugador". $conexion->error();
            }

        }else{
            $res.="No se pudo cargar la foto. Tenga en cuenta que la foto no debe superar los 3MB.";
        }

    }else{
        $res.="No se pudo cargar el documento de identidad. Tenga en cuenta que el documento debe estar en formato PDF y no debe superar los 3MB.";
    }

    echo "<script>alert('".$res."'); window.location.href='../../dashboard.php?proc=9&codigo_equipo=".$_POST["codigo_equipo_jugador"]."';</script>";
?>