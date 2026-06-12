<?php
	
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$sql="SELECT
	cc.codigo_credito,
	cc.fecha_pago,
	cc.valor_pago,
	cc.numero_cuota,
	dc.tiempo_cuota,
	dc.num_cuotas ultima_cuota,
	dc.total_pagar
	
	FROM tbl_deudores_creditos_cuotas cc
	
	INNER JOIN tbl_deudores_creditos dc
	ON dc.codigo_credito = cc.codigo_credito
	
	WHERE cc.codigo_cuota=" .$_POST["codigo_cuota"];
	$res=$conexion->ejecutar_sql($sql);

	if ($res->num_rows>0) {
		$registros=$res->fetch_all(MYSQLI_ASSOC);
		$reg = $registros[0];
		$valor_pagar = $reg["valor_pago"];
		$codigo_credito = $reg["codigo_credito"];
		$numero_cuota = $reg["numero_cuota"];
		$tiempo_cuota = $reg["tiempo_cuota"];
		$ultima_cuota = $reg["ultima_cuota"];
		$fecha_pago = $reg["fecha_pago"];
		$total_pagar = $reg["total_pagar"];
	}

	$resultado="";
	$valor_pagado=$_POST["valor_pagado"];

	$campos=array();
	$campos["valor_pagado"]=$valor_pagado;
	$valor_pago = $_POST["interes"] == 2 ? $valor_pagado : $valor_pagar;
	$campos["valor_pago"] = $valor_pago;
	$campos["fecha_registro_pago"]=$_POST["fecha_registro_pago"];
	$campos["comentarios"]=$_POST["comentarios"];
	$campos["usuario_registro_pago"]=$_SESSION["codigo_usuario"];

	$res=$conexion->actualizar("tbl_deudores_creditos_cuotas",$campos,"codigo_cuota='".$_POST["codigo_cuota"]."'");
	if ($res) {
		$resultado="Pago Registrado.";
		$fecha = $conexion->consultar_campo("tbl_deudores_creditos_cuotas","fecha_pago","codigo_credito='".$codigo_credito."' ORDER BY numero_cuota DESC LIMIT 1");
		$fecha_registro_pago = $_POST["fecha_registro_pago"];
		$dias_diferencia = (int)date_diff(date_create($fecha_registro_pago), date_create($fecha_pago))->format('%R%a');
		// Log temporal para depuración
		var_dump(["dias_diferencia" => $dias_diferencia, "fecha_registro_pago" => $fecha_registro_pago, "fecha_pago" => $fecha_pago]);
		$puntaje_base = 0;
		if ($dias_diferencia < 0) {
			// Pago retrasado
			$puntaje = $puntaje_base - abs($dias_diferencia) * 2;
			
		} elseif ($dias_diferencia == 0) {
			// Pago puntual
			$puntaje = $puntaje_base;
		} else {
			// Pago anticipado
			$puntaje = $puntaje_base + ($dias_diferencia * 5);
		}
		switch ($tiempo_cuota) {
			case '2':
				//FRECUENCIA QUINCENAL						
				$unidad="DAY";
				$tiempo=15;
			break;
			case '1':
				$unidad="MONTH";
				$tiempo=1;
			break;
		}

		if ($valor_pagar>$valor_pagado) {
			if ($_POST["interes"]==2) {
				$campos=array();
				$campos["codigo_credito"]=$codigo_credito;
				$fecha_pago_nueva_cuota = date("Y-m-d",strtotime($fecha[0]." + ".$tiempo."".$unidad));
				$campos["fecha_pago"]=$fecha_pago_nueva_cuota;
				$campos["numero_cuota"]=intval($ultima_cuota)+1;
				$campos["valor_pago"]=$valor_pagar;
				$res=$conexion->insertar("tbl_deudores_creditos_cuotas",$campos);

				$campos=array();				
				$valor_adicional = $total_pagar+$valor_pago;
				$campos["total_pagar"]=$valor_adicional;
				$campos["num_cuotas"]=intval($ultima_cuota)+1;
				$res=$conexion->actualizar("tbl_deudores_creditos",$campos,"codigo_credito='".$codigo_credito."'");

				// Actualizar puntaje y estado de la cuota siempre
				$campos = array();
				$campos["puntaje"] = $puntaje;
				$campos["estado"] = 2;
				$conexion->actualizar("tbl_deudores_creditos_cuotas", $campos, "codigo_cuota='".$_POST["codigo_cuota"]."'");
			}			
		}else{
			// Actualizar puntaje y estado de la cuota siempre
			$campos = array();
			$campos["puntaje"] = $puntaje;
			$campos["estado"] = 2;
			$conexion->actualizar("tbl_deudores_creditos_cuotas", $campos, "codigo_cuota='".$_POST["codigo_cuota"]."'");
			if ($numero_cuota==$ultima_cuota) {
				$campos=array();
				$campos["codigo_estado"]=2;
				$conexion->actualizar("tbl_deudores_creditos",$campos,"codigo_credito='".$codigo_credito."'");
			}
		}
	}

	echo $resultado;

?>