<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	function enviar_mail($correo, $asunto, $mensaje){

		$conexion=new conexion_db();

		//VARIABLES PARA EL ENVÍO
		$correo_destino = $correo;
		$subject = "SAF : ".$asunto;

		//ESTRUCTURA DEL MENSAJE
		$raiz_sitio='https://www.safenlinea.com';
		$contenido = 
			'<html>
				<head>
					<title>SAF NOTIFICACION</title>
				</head> 
				<body>
				<div style="width:100%;margin:0 auto;background-color: white">
					<div style="text-align:left;width:100%;background:#0069B5; padding: 15px;">
						<img style="width:14%" src="'.$raiz_sitio.'/img/saf_logo.png" />
					</div>
					<div style="margin:2%;">
						'.$mensaje.'
					</div>
					<div style="text-align:center;background-color:#0069B5;color:white; padding: 15px;">
						AVISO LEGAL<br/>
						Este mensaje de correo electrónico y los archivos adjuntos están dirigidos exclusivamente a los destinatarios especificados. Puede contener información confidencial o legalmente protegida. Si usted no es el destinatario, por favor le solicitamos que lo elimine. Se le informa que directa o indirectamente, usar, revelar, distribuir, imprimir o copiar alguna de las partes de este mensaje esta prohibido. Para fines de soporte legal los mensajes de correo electrónico están contemplados en la Ley 527 de 1999, en la cual se define y reglamenta el acceso y uso de los mensajes de datos por medios electrónicos.<br/>
						Derechos Reservados SAF
					</div>
				</div>
				</body>
			</html>';
			

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <webmaster@saf.com>' . "\r\n";
		$headers .= 'Cc: gerencia@saf.com' . "\r\n";           

		//ENVIAMOS MAIL
		if(mail($correo_destino,$subject,$contenido,$headers)){
			return true;
		}
		else{
			return false;
		}

	}

?>