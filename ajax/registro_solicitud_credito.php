<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/ajax/enviar_mail.php");

	$conexion=new conexion_db();
 
	$retorno="";

	$campos=array();
	$campos["nombres"]=$_POST["nombres"];
    $campos["apellidos"]=$_POST["apellidos"];
    $campos["documento"]=$_POST["documento"];
	$campos["direccion"]=$_POST["direccion"];
    $campos["email"]=$_POST["email"];
    $campos["telefono"]=$_POST["telefono"];
	$campos["valor_prestamo"]=$_POST["valor_prestamo"];
	$campos["tiempo_cuota"]=$_POST["tiempo_cuota"];
	$campos["num_cuotas"]=$_POST["num_cuotas"];
	$campos["tipo_interes"]=isset($_POST["tipo_interes"]) ? $_POST["tipo_interes"] : 1;
	$res=$conexion->insertar("tbl_deudores_creditos_solicitudes",$campos);	

	if ($res) {

		$correo_destino = "jairojesuspabonsuarez@gmail.com, servicioalclientesaf@gmail.com";
		$asunto = "SOLICITUD DE CREDITO";
		$mensaje = "<h1>SAF : Nueva Solicitud de Crédito</h1>";
		$mensaje .= "<p>Se ha registrado una nueva solictud de credito de <b>".$_POST['nombres']." ".$_POST['apellidos']."</b></p>";
		$mensaje .= "<p>Para responder esta solicitud haga click en el siguiente vinculo:</p><h2><a href='https://www.safenlinea.com/index.php?form=7'>Click Aquí</a></h2>";

		$envio_correo = enviar_mail($correo_destino, $asunto, $mensaje);

		if ($retorno=="") {
			$retorno="Solicitud de credito registrada correctamente";
		}

	}else{
		$retorno="No se registro la solicitud.".$conexion->error($res);	
	}

	echo $retorno;

?>