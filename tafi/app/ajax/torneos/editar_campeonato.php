<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();    
    $retorno=array();

    $sw=1;

    if( $sw==1){
        $datos=array();
        $datos["nombre_torneo"]=$_POST["nombre_torneo"];
        $datos["codigo_clasificacion"]=$_POST["codigo_clasificacion"];
        $datos["cantidad_rondas"]=$_POST["cantidad_rondas"];

        $datos["cantidad_equipos"]=$_POST["cantidad_equipos"];
        $datos["cantidad_jugadores_equipos"]=$_POST["cantidad_jugadores_equipos"];
        $datos["cantidad_jugadores_cancha"]=$_POST["cantidad_jugadores_cancha"];
        $datos["fecha_inicio"]=$_POST["fecha_inicio"];
        $datos["fecha_final"]=$_POST["fecha_final"];
        $datos["fecha_limite_equipos"]=$_POST["fecha_limite_equipos"];
        $datos["fecha_limite_jugadores"]=$_POST["fecha_limite_jugadores"];

        $resultado=$conexion->actualizar("tbl_tafi_torneos", $datos,"codigo_torneo='".$_POST["codigo_torneo"]."'");

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
                    $filtro="codigo_torneo='".$_POST["codigo_torneo"]."' AND codigo_concepto='".$v["codigo_conceptos"]."'";

                    $datos=array();
                    $datos["valor"]=$_POST["costo_".$v["codigo_conceptos"]];

                    $resultado=$conexion->actualizar("tbl_tafi_torneos_costos", $datos,$filtro);

                }

                $i=0;
                foreach ($_POST["sede"] as $key => $value) {
                    
                    $datos=array();
                    $datos["nombre"]=$value;
                    $datos["direccion"]=$_POST["direccion_sede"][$i];


                    if($_POST["codigo_sede"][$i]>0){
                        $datos["codigo_torneo"]=$_POST["codigo_torneo"];
                        $conexion->actualizar("tbl_tafi_torneos_sedes", $datos,"codigo_sede='".$_POST["codigo_sede"][$i]."'");
                    }else{
                        $datos["codigo_torneo"]=$_POST["codigo_torneo"];
                        $conexion->insertar("tbl_tafi_torneos_sedes", $datos);
                    }
                    
    
                    $i++;
                }

                
                $res.="Campeonato editado correctamente";
                            
            }else{
                $res.="Campeonato editado correctamente";
            }

        }else{
            $res.="No se pudo editar el campeonato";
        }
    }

    //redireccionar con un alert
    echo "<script>alert('".$res."'); window.location.href='../../dashboard.php?proc=4';</script>";
?>