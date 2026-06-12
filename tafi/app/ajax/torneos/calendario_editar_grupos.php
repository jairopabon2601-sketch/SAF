<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/enviar_mail.php");

    $conexion=new conexion_db();
    $retorno="";
    
    $i=0;
    foreach($_POST["codigo_calendario"] as $codigo_calendario){
       
        $fecha=$_POST["fecha_".$codigo_calendario];
        $hora=$_POST["hora_".$codigo_calendario];

        if($fecha!="" || $hora!=""){
            $datos=array();

            if($_POST["fecha_".$codigo_calendario]!=""){
                $datos["fecha"]=$_POST["fecha_".$codigo_calendario];
            }

            if($_POST["hora_".$codigo_calendario]!=""){
                $datos["hora"]=$_POST["hora_".$codigo_calendario];
            }

            $datos["codigo_sede"]=$_POST["codigo_sede"][$i];
            
            $resultado=$conexion->actualizar("tbl_tafi_torneos_calendario", $datos, "codigo_calendario=".$codigo_calendario);
    
            if(!$resultado){
                $retorno.="No se pudo actualizar el calendario ".$codigo_calendario ."-".$conexion->error()."<br>";
            }else{
                if( $fecha!="" && $hora!="" && $_POST["codigo_sede"][$i]>0 ){

                    $sql="SELECT 

                    del.email AS email_local, 
                    delv.email AS email_visitante,
                    sed.nombre AS sede,
                    eq.nombre_equipo AS equipo_local,
                    eqv.nombre_equipo AS equipo_visitante
                    
                    
                    FROM  tbl_tafi_torneos_calendario cal 
                    
                    INNER JOIN tbl_tafi_equipos eq ON 
                    cal.codigo_local=eq.codigo_equipo
                    
                    INNER JOIN tbl_tafi_delegados del ON 
                    eq.codigo_delegado=del.codigo_delegado
                    
                    INNER JOIN tbl_tafi_equipos eqv ON 
                    cal.codigo_visitante=eqv.codigo_equipo
                    
                    INNER JOIN tbl_tafi_delegados delv ON 
                    eqv.codigo_delegado=delv.codigo_delegado

                    INNER JOIN tbl_tafi_torneos_sedes sed ON 
                    cal.codigo_sede=sed.codigo_sede
                    
                    WHERE cal.codigo_calendario='".$codigo_calendario."'
                        AND cal.notificado=0";
                    
                    $resultado=$conexion->ejecutar_sql($sql);

                    if($resultado->num_rows>0){
                        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
                        $correo_local=$datos[0]["email_local"];
                        $correo_visitante=$datos[0]["email_visitante"];
                        $sede=$datos[0]["sede"];

                          //SE NOTIFICA A LOS USUARIOS QUE ESTAN INSCRITOS EN EL TORNEO
                        $subject = "Confirmacion de partido.";

                        $mensaje_respuesta="Se ha confirmado el partido para el dia ".$fecha." a las ".$hora." en la sede ".$sede.".<br><br>";
                        $mensaje_respuesta.="Equipo local: ".$datos[0]["equipo_local"]."<br>";
                        $mensaje_respuesta.="Equipo visitante: ".$datos[0]["equipo_visitante"]."<br><br>";

                        enviar_mail($correo_local,$subject,$mensaje_respuesta);
                        enviar_mail($correo_visitante,$subject,$mensaje_respuesta);
                        
                        $datos=array();
                        $datos["notificado"]=1;

                        $conexion->actualizar("tbl_tafi_torneos_calendario", $datos, "codigo_calendario=".$codigo_calendario);
                    }

                  


                }
            }


        }
        
        $i++;
    }

    echo $retorno;
?>