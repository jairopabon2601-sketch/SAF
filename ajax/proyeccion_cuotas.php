<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_prestamo = $_POST["fecha_prestamo"];
    $valor_prestamo = floatval($_POST["valor_prestamo"]);
    $num_cuotas = intval($_POST["num_cuotas"]);
    $tiempo_cuota = $_POST["tiempo_cuota"];
    $tasa = isset($_POST["tasa"]) ? floatval($_POST["tasa"]) : 0;
    $tipo_interes = isset($_POST["tipo_interes"]) ? intval($_POST["tipo_interes"]) : 1; // 1=fijo, 2=variable

    $tasa_decimal = $tasa / 100;
    $proyeccion = array();
    $fecha = $fecha_prestamo;
    $saldo_restante = $valor_prestamo;

    for ($i = 1; $i <= $num_cuotas; $i++) {
        switch ($tiempo_cuota) {
            case '2':
                $unidad = "DAY";
                $tiempo = 15;
                break;
            case '1':
                $unidad = "MONTH";
                $tiempo = 1;
                break;
            case '4':
                $unidad = "DAY";
                $tiempo = 7;
                break;
            default:
                $unidad = "MONTH";
                $tiempo = 1;
                break;
        }
        
        $fecha = date("Y-m-d", strtotime($fecha . " + " . $tiempo . " " . $unidad));
        
        if ($tipo_interes == 1) {
            // Interés Fijo - cálculo actual
            $total_pagar = isset($_POST["total_pagar"]) ? floatval($_POST["total_pagar"]) : 0;
            $valor_pago = ceil($total_pagar / $num_cuotas / 100) * 100;
        } else {
            // Interés Variable - calcular sobre saldo restante
            $interes_cuota = $saldo_restante * $tasa_decimal;
            $amortizacion = $valor_prestamo / $num_cuotas;
            $valor_pago = ceil(($amortizacion + $interes_cuota) / 100) * 100;
            $saldo_restante -= $amortizacion;
        }
        
        $proyeccion[] = array(
            'fecha' => $fecha,
            'valor' => $valor_pago
        );
    }
    header('Content-Type: application/json');
    echo json_encode($proyeccion);
    exit;
} 