<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<form name="enviador" method="post" action="ftp_archivo.php" enctype="multipart/form-data">

		<input type="hidden" name="MAX_FILE_SIZE" value="13156">
		Archivo: <input type="file" name="archivo">
		<input type="submit" value="Enviar">

	</form>
</body>
</html>