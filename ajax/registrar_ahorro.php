<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$retorno="";

	$datos_ahorros_meses=$conexion->buscar("tbl_ahorro_anyos_meses","codigo_ahorro_anyo='".$_POST["codigo_anio_ahorro"]."' ORDER BY orden_cuota");

	if ($datos_ahorros_meses) {
	
		$campos=array();
		$campos["codigo_ahorrador"]=$_POST["codigo_ahorrador"];
		$campos["codigo_ahorro_anyo"]=$_POST["codigo_anio_ahorro"];
		$campos["fecha_ingreso"]=$_POST["fecha_ingreso"];
		$campos["valor_pactado"]=$_POST["valor_pactado"];
		$campos["usuario_sistema"]=$_SESSION["codigo_usuario"];

		$res=$conexion->insertar("tbl_ahorradores_ahorros",$campos);	

		if ($res) {
			$id_ahorro=$conexion->insert_id();

			foreach ($datos_ahorros_meses as $datos) {

				if ($_POST["fecha_ingreso"]<=$datos["fecha_cuota"]) {

					$campos=array();
					$campos["codigo_ahorro"]=$id_ahorro;
					$campos["codigo_ahorro_mes"]=$datos["codigo_ahorro_mes"];
					$campos["codigo_mes"]=$datos["codigo_mes"];
					$campos["fecha_cuota"]=$datos["fecha_cuota"];

					$res=$conexion->insertar("tbl_ahorradores_cuotas",$campos);

					if ($res==false) {
						$retorno.="Error al grabar el mes: " .$i;
					}

				}
			}

			if ($retorno=="") {
				$retorno="Ahorra Creado";
			}

		}else{
			$retorno="No se registro el ahorrador.";	
		}
		
	}else{
		$retorno="No se encontro el año";
	}


	echo $retorno;

?>