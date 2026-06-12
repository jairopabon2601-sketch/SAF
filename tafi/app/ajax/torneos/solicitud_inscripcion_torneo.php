<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/enviar_mail.php");
    
    $conexion=new conexion_db();   

    $resultado="";

    $codigo_torneo=$_POST["codigo_torneo"];
    $codigo_equipo=$_POST["codigo_equipo_sol"]; 

    //CORREO DEL

    $sql="SELECT 

    * 
    
    FROM tbl_tafi_torneos_equipos te 
    
    WHERE te.codigo_torneo='".$codigo_torneo."'
    AND te.codigo_equipo='".$codigo_equipo."'";

    $resultado=$conexion->ejecutar_sql($sql);
            
    if($resultado->num_rows>0){
        $resultado="El equipo ya se encuentra inscrito en el torneo";
    }else{
        //SE VALIDA SI TIENE UNA SOLICITUD EN PROCESO

        $sql="SELECT * FROM tbl_tafi_torneos_solicitudes WHERE codigo_torneo='".$codigo_torneo."' AND codigo_equipo='".$codigo_equipo."' AND (codigo_estado=1 OR codigo_estado=2)";
        $resultado_val=$conexion->ejecutar_sql($sql);

        if($resultado_val->num_rows>0){
            $resultado="Ya existe una solicitud de inscripción en proceso";
        }else{

            $datos=array();
            $datos["codigo_torneo"]=$codigo_torneo;
            $datos["codigo_equipo"]=$codigo_equipo;
            $datos["codigo_estado"]=1;
            $datos["fecha_solicitud"]=date("Y-m-d H:i:s");
            $datos["codigo_solicitante"]=$_SESSION["codigo_origen"];

            $resultado=$conexion->insertar("tbl_tafi_torneos_solicitudes", $datos);

            if($resultado){
                $resultado="Solicitud de inscripción enviada";

                //SE ENVIA NOTIFICACION AL DELEGADO 
                $sql="SELECT 
                del.email
                FROM tbl_tafi_torneos tor 
                INNER JOIN tbl_tafi_contactos_web del ON 
                tor.codigo_responsable=del.codigo
                WHERE tor.codigo_torneo='".$codigo_torneo."'";
                $resultado2=$conexion->ejecutar_sql($sql);
                if($resultado2->num_rows>0){
                    
                    $datos=$resultado2->fetch_all(MYSQLI_ASSOC);
                    $email=$datos[0]["email"];

                    $subject = "Solicitud de inscripción a torneo";

                    $txt = "Se ha recibido una solicitud de inscripción a un torneo, por favor ingrese al sistema para aprobar o rechazar la solicitud";

                    $headers = "From: servicioalcliente@safenlinea.com " . "\r\n" .
                            "CC: servicioalcliente@safenlinea.com ";
                    

                    enviar_mail($email,$subject,$txt);		
                }

            }else{
                $resultado="Error al enviar la solicitud";
            }
        }

    }

    echo $resultado;
?>