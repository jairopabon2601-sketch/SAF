<?php
	$cid = ftp_connect("www.fcomier.net");

	$resultado = ftp_login($cid, "","");	

	if ((!$cid) || (!$resultado)) {
		echo "Fallo en la conexión"; die;
	} else {
		echo "Conectado.";

		ftp_pasv ($cid, true) ;
	
		/*$local = $_FILES["archivo"]["name"];
		$remoto = $_FILES["archivo"]["tmp_name"];
		$tama = $_FILES["archivo"]["size"];*/

		//echo $_SERVER["DOCUMENT_ROOT"];

		//$file=$_SERVER["DOCUMENT_ROOT"]."/img/undraw_posting_photo.svg";
		$remote_file="/".$local; 

		print_r($_FILES["archivo"]["name"]);

		print_r( $_FILES["archivo"]["tmp_name"]);


		if (ftp_put($cid, $_FILES["archivo"]["name"] , $_FILES["archivo"]["tmp_name"], FTP_BINARY)) {
		 echo "se ha cargado  con éxito\n";
		} else {
		 echo "Hubo un problema durante la transferencia de\n";
		}

		
		/*if (!$tama<=$_POST["MAX_FILE_SIZE"]){
			echo "Excede el tamaño del archivo...<br />";

		} else {*/

		/*	if (is_uploaded_file($remote_file)){
				copy($remoto, $remote_file);
				echo "Ruta: " . $remote_file;		
			}
			else {
				echo "no se pudo subir el archivo " . $local;
			}*/

		//}

		/*ftp_close($cid);*/
	}
?>