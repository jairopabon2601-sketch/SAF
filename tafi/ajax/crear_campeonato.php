<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

  
    $res="";

    $nombre_archivo = $_FILES['reglamento']['name'];
    $tipo_archivo = $_FILES['reglamento']['type'];
    $tamano_archivo = $_FILES['reglamento']['size'];

    $conexion=new conexion_db();    
    $retorno=array();

    $sw=1;

    if ($_SESSION["administrador"]!=1){

        $sql="SELECT * from tbl_tafi_contactos_web cw
        where cw.codigo='".$_SESSION["codigo_origen"]."' and cw.permite_crear_torneo=1";

        $resultado=$conexion->ejecutar_sql($sql);

        if($resultado->num_rows<=0){
            $res.="No tiene permisos para crear campeonatos";
            $sw=0;
        }
    }

    if( $sw==1){
        $datos=array();
        $datos["nombre_torneo"]=$_POST["nombre_torneo"];
        $datos["formato_competencias"]=$_POST["formato_competencias"];
        $datos["cantidad_equipos"]=$_POST["cantidad_equipos"];
        $datos["cantidad_jugadores_equipos"]=$_POST["cantidad_jugadores_equipos"];
        $datos["fecha_inicio"]=$_POST["fecha_inicio"];
        $datos["fecha_final"]=$_POST["fecha_final"];
        $datos["codigo_responsable"]=$_SESSION["codigo_origen"];

        $resultado=$conexion->insertar("tbl_tafi_torneos", $datos);

        if($resultado){
            $codigo_torneo=$conexion->insert_id();

            $sql="SELECT 

            con.codigo_conceptos,
            con.conceptos
            
            FROM tbl_tafi_tipo_torneos_costos ttc 
            
            INNER JOIN tbl_tafi_torneos_costos_conceptos con ON 
            ttc.codigo_concepto=con.codigo_conceptos
            
            WHERE ttc.codigo_tipo_torneo=1";
            
            $resultado=$conexion->ejecutar_sql($sql);
            
            if($resultado->num_rows>0){
                $datos=$resultado->fetch_all(MYSQLI_ASSOC);

                foreach( $datos as $k=>$v){
                    $datos=array();
                    $datos["codigo_torneo"]=$codigo_torneo;
                    $datos["codigo_concepto"]=$v["codigo_conceptos"];
                    $datos["valor"]=$_POST["costo_".$v["codigo_conceptos"]];

                    $resultado=$conexion->insertar("tbl_tafi_torneos_costos", $datos);

                }

                if($_SESSION["administrador"]!=1){
                    validacion_creacion_torneo();

                    if (!((strpos($tipo_archivo, "pdf")) && ($tamano_archivo < 300000))) {
                        $resultado.= "La extensión o el tamaño de los archivos no es correcta. Se permiten archivos .pdf <br><li>se permiten archivos de 3MB.";
                    }else{
                        if (move_uploaded_file($_FILES['reglamento']['tmp_name'],  "../reglamentos/reglamento_".$codigo_torneo.".pdf")){
                            //SE ACTUALIZA EL REGISTRO
                            $res.="Campeonato creado correctamente";
                            
                            $datos=array();
                            $datos["nombre_reglamento"]="reglamento_".$codigo_torneo.".pdf";
                            $conexion->actualizar("tbl_tafi_torneos", $datos,"codigo_torneo=".$codigo_torneo);
                            
                        }else{
                            $resultado.="Ocurrió algún error al subir el fichero. No pudo guardarse.";
                        }
                    }
                
                }
            }else{
                $res.="Campeonato creado correctamente";
            }

            if($_SESSION["administrador"]!=1){
                validacion_creacion_torneo();
            }
        }else{
            $res.="No se pudo crear el campeonato";
        }
    }

    //redireccionar con un alert
    echo "<script>alert('".$res."'); window.location.href='../dashboard.php?proc=4';</script>";

    function validacion_creacion_torneo(){
        $conexion=new conexion_db();    
        $retorno=array();

        $datos=array();
        $datos["permite_crear_torneo"]=0;

        $resultado=$conexion->actualizar("tbl_tafi_contactos_web", $datos,"codigo=".$_SESSION["codigo_origen"]);

        if($resultado){
            $retorno["resultado"]=1;
        }else{
            $retorno["resultado"]=0;
            $retorno["mensaje"]="No se pudo actualizar el estado de la solicitud";
        }

        return $retorno;
    }

?>