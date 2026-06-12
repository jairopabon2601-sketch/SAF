<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/conexion/conexion.php");


$GLOBALS['conexion'] = new conexion_db();


class usuarios
{

	protected $conexion;

	public function __construct()
	{

		global $GLOBALS;
		$this->conexion = $GLOBALS;

	}

	public function login($email, $pass)
	{


		if (!empty($email) && !empty($pass)) {
			$con = $this->conexion['conexion'];

			$pass = md5("PASSSAF" . trim($pass));
			$usuario = $email;

			$query = " SELECT 
				u.codigo_usuario,
				u.codigo_origen,
				u.codigo_tipo_usuario,
				u.codigo_perfil,
				ut.tabla_origen,
				ut.campo_tabla_origen

				FROM tbl_usuarios u

				inner join tbl_usuarios_tipos ut on 
				u.codigo_tipo_usuario=ut.codigo_tipo_usuario

				where u.pass = '$pass' and u.usuario = '$usuario' and u.codigo_estado=1";

			$resultado = $con->ejecutar_sql($query);

			if ($resultado->num_rows == 1) {

				$resultado = $resultado->fetch_all(MYSQLI_ASSOC);

				// INICIO SESIÓN (web - lo dejas)
				$_SESSION["codigo_usuario"] = $resultado[0]["codigo_usuario"];
				$_SESSION["codigo_origen"] = $resultado[0]["codigo_origen"];
				$_SESSION["codigo_tipo_usuario"] = $resultado[0]["codigo_tipo_usuario"];
				$_SESSION["tabla_origen"] = $resultado[0]["tabla_origen"];
				$_SESSION["campo_tabla_origen"] = $resultado[0]["campo_tabla_origen"];
				$_SESSION["codigo_perfil"] = $resultado[0]["codigo_perfil"];

				// 🔥 RESPUESTA PARA API (LO NUEVO)
				$usuario_data = [
					"codigo_usuario" => $resultado[0]["codigo_usuario"],
					"codigo_tipo_usuario" => $resultado[0]["codigo_tipo_usuario"],
					"codigo_perfil" => $resultado[0]["codigo_perfil"]
				];

				// generar token
				$token = $this->generarToken($usuario_data);

				$retorno["resultado"] = 1;
				$retorno["token"] = $token;
				$retorno["usuario"] = $usuario_data;


			} else {
				$retorno["mensaje"] = "Error en el usuario o la contraseña.";
				$retorno["resultado"] = 0;
			}

		}

		return $retorno;
	}

	public function registrar_usuario(array $data)
	{


		if (!empty($data)) {
			$con = $this->conexion['conexion'];

			$trimmed_data = array_map('trim', $data);

			$clave_aleatoria = $this->randomPassword();

			$campos = array();
			$campos['usuario'] = $trimmed_data['usuario'];
			$campos['pass'] = md5("PASSSAF" . trim($clave_aleatoria));
			$campos['codigo_perfil'] = $trimmed_data['codigo_perfil'];
			$campos['codigo_tipo_usuario'] = $trimmed_data['codigo_tipo_usuario'];
			$campos['codigo_origen'] = $trimmed_data['codigo_origen'];

			$resultado = $con->insertar('tbl_usuarios', $campos);

			if ($resultado) {

				$email = $trimmed_data['usuario'];

				$subject = "Bienvenido a SAF - Creacion de Usuario";

				$txt = '
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<style>
		body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; color: #333; }
		.container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
		.header { background: #47478D; padding: 30px 20px; text-align: center; color: white; }
		.logo { max-width: 150px; margin-bottom: 15px; }
		.content { padding: 40px 30px; line-height: 1.6; }
		.cred-box { background: #f8f9fa; border-left: 4px solid #47478D; padding: 20px; margin: 25px 0; border-radius: 4px; }
		.cred-box p { margin: 10px 0; font-size: 16px; }
		.footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 13px; color: #666; border-top: 1px solid #eee; }
		.btn { display: inline-block; padding: 12px 25px; background: #47478D; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<img src="https://safenlinea.com/img/icons/saf_isotipo.png" alt="SAF Logo" class="logo" onerror="this.style.display=\'none\'">
			<h1 style="margin: 0; font-size: 24px;">¡Bienvenido a SAF!</h1>
		</div>
		<div class="content">
			<p>Hola,</p>
			<p>Tu cuenta ha sido creada exitosamente. A continuación, encontrarás tus credenciales de acceso al sistema:</p>
			
			<div class="cred-box">
				<p><b>Usuario:</b> ' . $email . '</p>
				<p><b>Contraseña:</b> ' . $clave_aleatoria . '</p>
			</div>
			
			<p>Te recomendamos cambiar esta contraseña proporcionada por el sistema en cuanto inicies sesión por motivos de seguridad.</p>
			
			<center>
				<a href="https://safenlinea.com" class="btn">Ingresar al Sistema</a>
			</center>
		</div>
		<div class="footer">
			<p>Este es un mensaje generado automáticamente, por favor no respondas a este correo.</p>
			<p>&copy; ' . date('Y') . ' SAF. Todos los derechos reservados.</p>
		</div>
	</div>
</body>
</html>
';

				$headers = "From: SAF <servicioalcliente@safenlinea.com>\r\n";
				$headers .= "CC: servicioalcliente@safenlinea.com\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

				mail($email, $subject, $txt, $headers);

				return "Se ha generado el usuario.";

			} else {
				return "No se generó el usuario.";
			}

		} else {
			return "No se cargaron los datos.";
		}
	}

	public function forgetPassword($email, $codigo_tipo_usuario)
	{

		if (!empty($email)) {
			$con = $this->conexion['conexion'];

			$clave_aleatoria = $this->randomPassword();
			$password = md5("PASSSAF" . trim($clave_aleatoria));

			$query = " SELECT *

				FROM tbl_usuarios u

				where u.usuario = '$email' and u.codigo_tipo_usuario='$codigo_tipo_usuario' and u.codigo_estado=1";


			$resultado = $con->ejecutar_sql($query);

			if ($resultado->num_rows == 1) {

				$resultado = $resultado->fetch_all(MYSQLI_ASSOC);

				$filtro_update = "usuario = '$email' and codigo_tipo_usuario='$codigo_tipo_usuario'";
				$campos = array();
				$campos['pass'] = $password;

				$resultado = $con->actualizar("tbl_usuarios", $campos, $filtro_update);

				if ($resultado) {

					$subject = "SAF: Recuperación de Contraseña";

					$txt = '
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<style>
		body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; color: #333; }
		.container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
		.header { background: #47478D; padding: 30px 20px; text-align: center; color: white; }
		.logo { max-width: 150px; margin-bottom: 15px; }
		.content { padding: 40px 30px; line-height: 1.6; }
		.cred-box { background: #f8f9fa; border-left: 4px solid #47478D; padding: 20px; margin: 25px 0; border-radius: 4px; }
		.cred-box p { margin: 10px 0; font-size: 16px; }
		.footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 13px; color: #666; border-top: 1px solid #eee; }
		.btn { display: inline-block; padding: 12px 25px; background: #47478D; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<img src="https://safenlinea.com/img/icons/saf_isotipo.png" alt="SAF Logo" class="logo" onerror="this.style.display=\'none\'">
			<h1 style="margin: 0; font-size: 24px;">Cambio de Contraseña</h1>
		</div>
		<div class="content">
			<p>Hola,</p>
			<p>Se ha generado una nueva contraseña para tu cuenta en SAF. Aquí tienes tus nuevos datos de acceso para ingresar al sistema:</p>
			
			<div class="cred-box">
				<p><b>Usuario:</b> ' . $email . '</p>
				<p><b>Nueva Contraseña:</b> ' . $clave_aleatoria . '</p>
			</div>
			
			<p>Te recomendamos ingresar al sistema y cambiar esta contraseña proporcionada lo antes posible por una de tu preferencia.</p>
			
			<center>
				<a href="https://safenlinea.com" class="btn">Ingresar al Sistema</a>
			</center>
		</div>
		<div class="footer">
			<p>Este es un mensaje generado automáticamente, por favor no respondas a este correo.</p>
			<p>&copy; ' . date('Y') . ' SAF. Todos los derechos reservados.</p>
		</div>
	</div>
</body>
</html>
';

					$headers = "From: SAF <servicioalcliente@safenlinea.com>\r\n";
					$headers .= "CC: servicioalcliente@safenlinea.com\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

					mail($email, $subject, $txt, $headers);

					$retorno["mensaje"] = "Contraseña actualizada, fué envida a su correo " . $email;
					$retorno["resultado"] = 1;
				} else {
					$retorno["mensaje"] = "Error al actualizar la contraseña";
					$retorno["resultado"] = 0;
				}

			} else {
				$retorno["mensaje"] = "No se encontró el usuario o se encuentra Inactivo.";
				$retorno["resultado"] = 0;
			}
		} else {
			$retorno["mensaje"] = "No se cargó el email.";
			$retorno["resultado"] = 0;
		}

		return $retorno;
	}

	private function randomPassword()
	{
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //recuerde que debe declarar $pass como un array
		$alphaLength = strlen($alphabet) - 1; //poner la longitud -1 en caché
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //convertir el array en una cadena
	}




	public function cerrar_session()
	{
		session_start();
		session_unset();
		session_destroy();

		header('Location: index.html');
	}


	public function generarToken($data)
	{

		$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
		$payload = [
			"codigo_usuario" => $data["codigo_usuario"],
			"codigo_tipo_usuario" => $data["codigo_tipo_usuario"],
			"codigo_perfil" => $data["codigo_perfil"],
			"exp" => time() + (60 * 60 * 24)
		];

		$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
		$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

		$secret = "CLAVE_SUPER_SECRETA_123"; // cámbiala luego

		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
		$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

		return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
	}

	public function validarToken()
	{
		$authHeader = '';
		$headers = function_exists('getallheaders') ? getallheaders() : [];


		if (isset($headers['Authorization'])) {
			$authHeader = $headers['Authorization'];
		} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
		} elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
			$authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
		}

		if (!$authHeader) {
			echo json_encode(["error" => "Token requerido"]);
			exit;
		}

		$token = str_replace("Bearer ", "", $authHeader);
		$partes = explode(".", $token);

		if (count($partes) != 3) {
			echo json_encode(["error" => "Token inválido"]);
			exit;
		}

		list($header64, $payload64, $signature64) = $partes;

		// Verificar Firma
		$secret = "CLAVE_SUPER_SECRETA_123";
		$signature = hash_hmac('sha256', $header64 . "." . $payload64, $secret, true);
		$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

		if ($base64UrlSignature !== $signature64) {
			echo json_encode(["error" => "Firma de token no válida"]);
			exit;
		}

		$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload64)), true);

		if ($payload['exp'] < time()) {
			echo json_encode(["error" => "Token expirado"]);
			exit;
		}

		return $payload;
	}





}