<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	function enviar_mail($correo, $asunto, $mensaje){
		
		// Validar que el correo sea válido
		if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
			error_log("SAF - Error: Email inválido: " . $correo);
			return false;
		}

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
					<meta charset="UTF-8">
				</head> 
				<body>
				<div style="width:100%;margin:0 auto;background-color: white; font-family: Arial, sans-serif;">
					<div style="text-align:left;width:100%;background:#0069B5; padding: 15px;">
						<img style="width:14%" src="'.$raiz_sitio.'/img/saf_logo.png" alt="SAF Logo" />
					</div>
					<div style="margin:2%; padding: 20px;">
						'.$mensaje.'
					</div>
					<div style="text-align:center;background-color:#0069B5;color:white; padding: 15px; font-size: 12px;">
						AVISO LEGAL<br/>
						Este mensaje de correo electrónico y los archivos adjuntos están dirigidos exclusivamente a los destinatarios especificados. Puede contener información confidencial o legalmente protegida. Si usted no es el destinatario, por favor le solicitamos que lo elimine. Se le informa que directa o indirectamente, usar, revelar, distribuir, imprimir o copiar alguna de las partes de este mensaje esta prohibido. Para fines de soporte legal los mensajes de correo electrónico están contemplados en la Ley 527 de 1999, en la cual se define y reglamenta el acceso y uso de los mensajes de datos por medios electrónicos.<br/>
						Derechos Reservados SAF
					</div>
				</div>
				</body>
			</html>';
			

		// Configuración mejorada de headers
		$headers = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=UTF-8";
		$headers[] = "From: SAF Sistema <noreply@safenlinea.com>";
		$headers[] = "Reply-To: noreply@safenlinea.com";
		$headers[] = "X-Mailer: PHP/" . phpversion();
		$headers[] = "X-Priority: 3";
		$headers[] = "X-MSMail-Priority: Normal";
		$headers[] = "Importance: Normal";

		// Convertir array de headers a string
		$headers_string = implode("\r\n", $headers);

		// Log del intento de envío
		error_log("SAF - Intentando enviar correo a: " . $correo_destino . " - Asunto: " . $subject);

		//ENVIAMOS MAIL
		$resultado = mail($correo_destino, $subject, $contenido, $headers_string);
		
		if($resultado){
			error_log("SAF - Correo enviado exitosamente a: " . $correo_destino);
			return true;
		}
		else{
			error_log("SAF - Error al enviar correo a: " . $correo_destino);
			
			// Intentar obtener información del error
			$error = error_get_last();
			if ($error) {
				error_log("SAF - Detalle del error: " . print_r($error, true));
			}
			
			return false;
		}
	}

	// Función para verificar la configuración de correo
	function verificar_configuracion_correo() {
		$configuracion = array();
		
		// Verificar si la función mail está disponible
		$configuracion['mail_function'] = function_exists('mail');
		
		// Verificar configuración de PHP
		$configuracion['sendmail_path'] = ini_get('sendmail_path');
		$configuracion['smtp_host'] = ini_get('SMTP');
		$configuracion['smtp_port'] = ini_get('smtp_port');
		
		// Verificar permisos de escritura en logs
		$configuracion['error_log_writable'] = is_writable(ini_get('error_log'));
		
		return $configuracion;
	}

?>