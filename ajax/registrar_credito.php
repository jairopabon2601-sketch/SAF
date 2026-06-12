<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$fecha_prestamo = $_POST["fecha_prestamo"];

	$retorno="";

	$total_pagar = parseInt($_POST["total_pagar"]);

	$campos=array();
	$campos["codigo_deudor"]=$_POST["codigo_deudor"];
	$campos["fecha_prestamo"]=$fecha_prestamo;
	$campos["valor_prestamo"]=$_POST["valor_prestamo"];
	$campos["total_pagar"]=$total_pagar;
	$campos["tiempo_cuota"]=$_POST["tiempo_cuota"];
	$campos["num_cuotas"]=$_POST["num_cuotas"];
	$campos["codigo_tasa_interes"]=$_POST["codigo_tasa_interes_reg"];
	$campos["fuente_credito"] = $_POST["fuente_credito_reg"];
	$campos["tipo_interes"] = $_POST["tipo_interes"];
	$res=$conexion->insertar("tbl_deudores_creditos",$campos);	

	if ($res) {
		$codigo_credito=$conexion->insert_id();

		$num_cuotas = $_POST["num_cuotas"];
		$tipo_interes = $_POST["tipo_interes"];
		$valor_prestamo = $_POST["valor_prestamo"];
		$tasa_interes = $_POST["codigo_tasa_interes_reg"];
		$tasa_decimal = $tasa_interes / 100;
		$saldo_restante = $valor_prestamo;
		
		for ($i=1; $i <= $num_cuotas ; $i++) {

			$tiempo_cuotas=$_POST["tiempo_cuota"];
				//echo("weer".$reg_requisito_legal["unidad"]."suas");
				switch ($tiempo_cuotas) {
					case '2':
						//FRECUENCIA QUINCENAL						
						$unidad="DAY";
						$tiempo=15;
					break;
					case '1':
						$unidad="MONTH";
						$tiempo=1;
					break;
					case '4':
						$unidad="DAY";
						$tiempo=7;
					break;
				}

			$fecha_prestamo = date("Y-m-d",strtotime($fecha_prestamo." + ".$tiempo."".$unidad));

			// Calcular valor de cuota según tipo de interés
			if ($tipo_interes == 1) {
				// Interés Fijo - valor constante
				$valor_pago = round($total_pagar/$num_cuotas);
			} else {
				// Interés Variable - calcular sobre saldo restante
				$interes_cuota = $saldo_restante * $tasa_decimal;
				$amortizacion = $valor_prestamo / $num_cuotas;
				$valor_pago = round($amortizacion + $interes_cuota);
				$saldo_restante -= $amortizacion;
			}

			$campos=array();
			$campos["codigo_credito"]=$codigo_credito;
			$campos["fecha_pago"]=$fecha_prestamo;
			$campos["numero_cuota"]=$i;
			$campos["valor_pago"]=$valor_pago;

			$res=$conexion->insertar("tbl_deudores_creditos_cuotas",$campos);

			if ($res==false) {
				$retorno.="Error al grabar cuota: " .$i;
			}
		}

		if ($retorno=="") {
			$retorno="Crédito Creado";
		}

	}else{
		$retorno="No se registro el deudor.".$conexion->error();	
	}

	echo $retorno;

	function parseInt($str) {
		// Elimina todos los caracteres que no sean dígitos
		$cleaned = preg_replace('/[^0-9]/', '', $str);
		return (int)$cleaned;
	}

?>