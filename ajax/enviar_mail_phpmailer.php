<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

// Función para enviar correo usando PHPMailer (si está disponible)
function enviar_mail_phpmailer($correo, $asunto, $mensaje) {
    
    // Verificar si PHPMailer está disponible
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log("SAF - PHPMailer no está disponible, usando función mail() nativa");
        return enviar_mail_nativo($correo, $asunto, $mensaje);
    }
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Cambiar por tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'tu-email@gmail.com'; // Cambiar por tu email
        $mail->Password = 'tu-password'; // Cambiar por tu password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Remitente
        $mail->setFrom('noreply@safenlinea.com', 'SAF Sistema');
        $mail->addReplyTo('noreply@safenlinea.com', 'SAF Sistema');
        
        // Destinatario
        $mail->addAddress($correo);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = "SAF : " . $asunto;
        
        // Estructura del mensaje
        $raiz_sitio = 'https://www.safenlinea.com';
        $contenido = '
        <html>
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
        
        $mail->Body = $contenido;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mensaje));
        
        $mail->send();
        error_log("SAF - Correo enviado exitosamente con PHPMailer a: " . $correo);
        return true;
        
    } catch (Exception $e) {
        error_log("SAF - Error con PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}

// Función nativa de PHP como respaldo
function enviar_mail_nativo($correo, $asunto, $mensaje) {
    
    // Validar que el correo sea válido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        error_log("SAF - Error: Email inválido: " . $correo);
        return false;
    }

    $subject = "SAF : " . $asunto;

    // Estructura del mensaje
    $raiz_sitio = 'https://www.safenlinea.com';
    $contenido = '
    <html>
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
    error_log("SAF - Intentando enviar correo nativo a: " . $correo . " - Asunto: " . $subject);

    // Enviar mail
    $resultado = mail($correo, $subject, $contenido, $headers_string);
    
    if($resultado){
        error_log("SAF - Correo enviado exitosamente (nativo) a: " . $correo);
        return true;
    }
    else{
        error_log("SAF - Error al enviar correo (nativo) a: " . $correo);
        
        // Intentar obtener información del error
        $error = error_get_last();
        if ($error) {
            error_log("SAF - Detalle del error: " . print_r($error, true));
        }
        
        return false;
    }
}

// Función principal que intenta usar PHPMailer primero, luego la función nativa
function enviar_mail($correo, $asunto, $mensaje) {
    // Intentar con PHPMailer primero
    $resultado = enviar_mail_phpmailer($correo, $asunto, $mensaje);
    
    if (!$resultado) {
        // Si falla, usar la función nativa
        error_log("SAF - PHPMailer falló, intentando con función nativa");
        $resultado = enviar_mail_nativo($correo, $asunto, $mensaje);
    }
    
    return $resultado;
}

// Función para verificar la configuración de correo
function verificar_configuracion_correo() {
    $configuracion = array();
    
    // Verificar si la función mail está disponible
    $configuracion['mail_function'] = function_exists('mail');
    
    // Verificar si PHPMailer está disponible
    $configuracion['phpmailer_available'] = class_exists('PHPMailer\PHPMailer\PHPMailer');
    
    // Verificar configuración de PHP
    $configuracion['sendmail_path'] = ini_get('sendmail_path');
    $configuracion['smtp_host'] = ini_get('SMTP');
    $configuracion['smtp_port'] = ini_get('smtp_port');
    
    // Verificar permisos de escritura en logs
    $configuracion['error_log_writable'] = is_writable(ini_get('error_log'));
    
    return $configuracion;
}

?> 