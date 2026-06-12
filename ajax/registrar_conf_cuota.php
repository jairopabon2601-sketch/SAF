<?php
	
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$retorno="";

	$campos=array();
	$campos["anyos"]=$_POST["anio"];
	$campos["fecha_inicio"]=$_POST["fecha_inicio"];
	$campos["fecha_fin"]=$_POST["fecha_final"];
	$campos["tiempo_meses"]=$_POST["tiempo_mes"];
	$campos["tipo"]=$_POST["tipo"];

	$res=$conexion->insertar("tbl_ahorro_anyos",$campos);	

	if ($res) {

		$id_ahorro_anyo=$conexion->insert_id();

		$fecha_inicio    = $_POST["fecha_inicio"];
		$fecha_fin      = $_POST["fecha_final"];

		$comienzo = new DateTime($fecha_inicio);
		$final = new DateTime($fecha_fin);

		$x=1;
		for($i = $comienzo; $i <= $final; $i->modify('+1 month')){

			$fecha_cuota=$i->format("Y-m-01");
			$numMes = date("n", strtotime($i->format("Y-m-01")));

			$campos=array();
			$campos["codigo_ahorro_anyo"]=$id_ahorro_anyo;
			$campos["codigo_mes"]=$numMes;
			$campos["fecha_cuota"]=$fecha_cuota;
			$campos["orden_cuota"]=$x;

			$res=$conexion->insertar("tbl_ahorro_anyos_meses",$campos);

			if ($res==false) {
				$retorno.="Error al grabar el la cuota " . $fecha_cuota;
			}
			
			$x++;
		}

		if ($retorno=="") {
			$retorno="Configuración Registrada";	
		}		
	}else{
		$retorno="No se registró la configuración.";	
	}

	echo $retorno;

?>