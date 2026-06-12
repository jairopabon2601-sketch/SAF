<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
	

	$conexion=new conexion_db();

	class usuarios {

		protected $conexion;

		public function __construct() {

        	$this->conexion =new conexion_db();
    	}

		public function nombre_usuario(){

			$sql="SELECT 
    
			".$_SESSION["campo_nombre_origen"]." 
		
			FROM ".$_SESSION["tabla_origen"]." 
			
			WHERE ".$_SESSION["campo_codigo_origen"]."=".$_SESSION["codigo_origen"];

			$resultado=$this->conexion->ejecutar_sql($sql);
			$datos_usuario=$resultado->fetch_all(MYSQLI_ASSOC);	

			return $datos_usuario[0][$_SESSION["campo_nombre_origen"]];
		}

		public function login($email,$pass){
			

			if( !empty($email) && !empty($pass) ){
		
				$pass =md5("PASSTAFI".trim($pass));
				$usuario = $email;

				$query = " SELECT 
				u.codigo_usuario,
				u.codigo_origen,
				u.codigo_tipo_usuario,
				ut.tabla_origen,
				ut.campo_codigo_origen,
				ut.campo_nombre_origen,
				ut.administrador,
				ut.nombre AS nombre_tipo_usuario,
				ut.proceso_inicio

				FROM tbl_tafi_usuarios u

				inner join tbl_tafi_usuarios_tipos ut on 
				u.codigo_tipo_usuario=ut.codigo_tipo_usuario

				where u.pass = '$pass' and u.usuario = '$usuario' and u.codigo_estado=1";

				$resultado=$this->conexion->ejecutar_sql($query);

				if($resultado->num_rows == 1){
					
					$resultado=$resultado->fetch_all(MYSQLI_ASSOC);	
					
					//INICIO LAS VARIABLES DE SESIONES
					$_SESSION["codigo_usuario"]=$resultado[0]["codigo_usuario"];
					$_SESSION["codigo_origen"]=$resultado[0]["codigo_origen"];
					$_SESSION["codigo_tipo_usuario"]=$resultado[0]["codigo_tipo_usuario"];
					$_SESSION["tabla_origen"]=$resultado[0]["tabla_origen"];
					$_SESSION["campo_codigo_origen"]=$resultado[0]["campo_codigo_origen"];
					$_SESSION["campo_nombre_origen"]=$resultado[0]["campo_nombre_origen"];
					$_SESSION["administrador"]=$resultado[0]["administrador"];
					$_SESSION["nombre_tipo_usuario"]=$resultado[0]["nombre_tipo_usuario"];
					
					$retorno["resultado"]=1;
					$retorno["proceso_inicio"]=$resultado[0]["proceso_inicio"];
				}else{
					$retorno["resultado"]=0;
				}

			}

			return $retorno;
		}

		public function registrar_usuario( array $data ){

			include($_SERVER['DOCUMENT_ROOT']."/tafi/app/php/enviar_mail.php");

			if( !empty( $data ) ){
				
				$trimmed_data = array_map('trim', $data);

				$clave_aleatoria=$this->randomPassword();

				$sql="SELECT indicativo_usuario FROM tbl_tafi_usuarios_tipos WHERE codigo_tipo_usuario='".$trimmed_data['codigo_tipo_usuario']."'";
				
				$resultado=$this->conexion->ejecutar_sql($sql);

				if($resultado->num_rows > 0){

					$resultado=$resultado->fetch_all(MYSQLI_ASSOC);	
					$indicativo_usuario=$resultado[0]["indicativo_usuario"];

					$usuario=$indicativo_usuario.$trimmed_data['usuario'];

					$campos=array();
					$campos['usuario'] =$usuario ;
					$campos['pass'] =md5("PASSTAFI".trim($clave_aleatoria));
					$campos['codigo_tipo_usuario'] = $trimmed_data['codigo_tipo_usuario'] ;
					$campos['codigo_origen'] = $trimmed_data['codigo_origen'] ;

					$resultado=$this->conexion->insertar('tbl_tafi_usuarios',$campos);
					
					if ($resultado) {

						$email = $trimmed_data['email'];

						$subject = "Creación de Usuario";
						$txt ="<b>USUARIO: </b>". $usuario."  <br><br>";
						$txt .= "<b>CONTRASEÑA:</b> </br>". $clave_aleatoria."";
						$headers = "From: servicioalcliente@safenlinea.com " . "\r\n" .
								"CC: servicioalcliente@safenlinea.com ";
						

						enviar_mail($email,$subject,$txt);		

						return "Se ha generado el usuario.";

					}else{
						return "No se generó el usuario.";
					}

				}else{
					return "No se encontró el tipo de usuario.";
				}
			}else{
				return "No se cargaron los datos.";
			}
		}

		public function forgetPassword($email,$codigo_tipo_usuario){

			/*if(!empty($email)){
				$con=$this->conexion['conexion'];
				
				$clave_aleatoria=$this->randomPassword();
				$password = md5("PASSSAF".trim($clave_aleatoria));

				$query = " SELECT *

				FROM tbl_usuarios u

				where u.usuario = '$email' and u.codigo_tipo_usuario='$codigo_tipo_usuario' and u.codigo_estado=1";

		
				$resultado=$con->ejecutar_sql($query);

				if($resultado->num_rows == 1){
						
					$resultado=$resultado->fetch_all(MYSQLI_ASSOC);	

					$filtro_update="usuario = '$email' and codigo_tipo_usuario='$codigo_tipo_usuario'";
					$campos=array();
					$campos['pass'] = $password;

					$resultado=$con->actualizar("tbl_usuarios",$campos,$filtro_update);

					if($resultado){

						$subject = "CAMBIO DE CONTRASEÑA";
						$txt ="USUARIO: ". $email;
						$txt .= "      CONTRASEÑA: ". $clave_aleatoria;
						$headers = "From: servicioalcliente@safenlinea.com " . "\r\n" .
								"CC: servicioalcliente@safenlinea.com ";
							
						mail($email,$subject,$txt,$headers);

						$retorno["mensaje"]="Contraseña actualizada, fué envida a su correo ". $email ;
						$retorno["resultado"]=1;	
					}else{
						$retorno["mensaje"]="Error al actualizar la contraseña";
						$retorno["resultado"]=0;
					}

				}else{
					$retorno["mensaje"]="No se encontró el usuario o se encuentra Inactivo.";
					$retorno["resultado"]=0;
				}
			}else{
				$retorno["mensaje"]="No se cargó el email.";
				$retorno["resultado"]=0;
			}*/

			return $retorno;
		}

		public function cambiar_contrasenia($codigo_usuario,$pass){
			$campos=array();
			$campos['pass'] =md5("PASSTAFI".trim($pass));
			$resultado=$this->conexion->actualizar('tbl_tafi_usuarios',$campos,"codigo_usuario=".$codigo_usuario);
			return $resultado;
		}

		public  function validar_contrasenia_usuario($codigo_usuario,$pass){

			$sql="SELECT pass FROM tbl_tafi_usuarios WHERE codigo_usuario='".$codigo_usuario."'";
			$resultado=$this->conexion->ejecutar_sql($sql);

			if($resultado){
				$datos=$resultado->fetch_all(MYSQLI_ASSOC);
				$password = $datos[0]["pass"];
				$password_enviada=md5("PASSTAFI".trim($pass));

				if($password==$password_enviada){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		} 

		public function validar_session(){
			if(isset($_SESSION["codigo_usuario"]) && isset($_SESSION["codigo_origen"]) && isset($_SESSION["codigo_tipo_usuario"])){
				return true;
			}else{
				header('Location: index.php');
			}
		}
		
		public function validar_permisos($codigo_formulario){
			if($codigo_formulario>0 && $_SESSION['administrador']==0){

				$sql="SELECT 

				IF(proc.requiere_permisos=1, COUNT(p.codigo),1) acceso
				
				FROM tbl_tafi_procesos proc 
				
				left JOIN tbl_tafi_usuarios_procesos p ON 
				proc.codigo_proceso=p.codigo_proceso
				AND p.codigo_tipo_usuario='".$_SESSION['codigo_tipo_usuario']."'
				
				WHERE proc.codigo_proceso ='".$codigo_formulario."'";

				$resul=$this->conexion->ejecutar_sql($sql);
				$acceso=$resul->fetch_array(MYSQLI_ASSOC);

				if($acceso['acceso']==0){
					echo "No tiene permisos para acceder a esta página.";
					exit();
				}
			}
		}


		private function randomPassword() {
			$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
			$pass = array(); //recuerde que debe declarar $pass como un array
			$alphaLength = strlen($alphabet) - 1; //poner la longitud -1 en caché
			for ($i = 0; $i < 8; $i++) {
				$n = rand(0, $alphaLength);
				$pass[] = $alphabet[$n];
			}
			return implode($pass); //convertir el array en una cadena
		}

	
		public function cerrar_session(){
			session_start();
			session_unset();	
			session_destroy();
	
			header('Location: index.php');
		}



	}
	
?>