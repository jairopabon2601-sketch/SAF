<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/enviar_mail.php");
    
    $conexion=new conexion_db();  

    $retorno="";
    $codigo_solicitud=$_POST["codigo_solicitud"];
    $codigo_torneo=$_POST["codigo_solicitud_torneo"];
    $codigo_equipo=$_POST["codigo_solicitud_equipo"];
    $correo_delegado="";
    $mensaje_respuesta="";

    //CORREO DEL DELEGADO 
    $sql="SELECT 
    del.email
    FROM tbl_tafi_equipos eq 
    INNER JOIN tbl_tafi_delegados del ON 
    eq.codigo_delegado=del.codigo_delegado
    WHERE eq.codigo_equipo=".$codigo_equipo;

    $resultado=$conexion->ejecutar_sql($sql);
                
    if($resultado->num_rows>0){
        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
        $correo_delegado=$datos[0]["email"];
    }

    //SE GRABA LA RESPUESTA
    $datos=array();
    $datos["respuesta"]=$_POST["respuesta"];
    $datos["fecha_respuesta"]=date("Y-m-d");
    $datos["usuario_respuesta"]=$_SESSION["codigo_origen"];
    $datos["codigo_estado"]=$_POST["codigo_estado"];

    $resultado=$conexion->actualizar("tbl_tafi_torneos_solicitudes", $datos, "codigo_solicitud='".$codigo_solicitud."'");

    if($resultado){

        if($_POST["codigo_estado"]==2){
            //SE REGISTRA EN EL TORNEO
            $datos=array();
            $datos["codigo_torneo"]=$codigo_torneo;
            $datos["codigo_equipo"]=$codigo_equipo;

            $resultado=$conexion->insertar("tbl_tafi_torneos_equipos", $datos);

            if($resultado){
                $retorno="Equipo registrado en el torneo";
                $mensaje_respuesta="Su solicitud de inscripción al torneo ha sido aprobada, su equipo ha sido registrado en el torneo.";

                //SE ENVIA NOTIFICACION AL DELEGADO
            }else{
                $retorno="No se pudo registrar el equipo en el torneo";
            }
        }else{
            $retorno="Respuesta grabada";

            $mensaje_respuesta="Su solicitud de inscripción al torneo ha sido rechazada, por favor comuniquese con el administrador del torneo.<br><br>";
            $mensaje_respuesta.="<b>Motivo:</b> ".$_POST["respuesta"];
        }

    }else{
        $retorno="No se pudo grabar la respuesta";
    }

    if($correo_delegado!=""){
        
        $subject = "Respuesta de inscripción a torneo";
        enviar_mail($correo_delegado,$subject,$mensaje_respuesta);
    }

    echo $retorno;

?>