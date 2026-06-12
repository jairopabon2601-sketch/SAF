<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

$conexion = new conexion_db();

$codigo_cuota = isset($_POST['codigo_cuota']) ? intval($_POST['codigo_cuota']) : 0;
$respuesta = ["success" => false];

if ($codigo_cuota > 0) {
    $sql = "SELECT 
                cc.codigo_credito, 
                cc.fecha_pago, 
                cc.valor_pago, 
                cc.numero_cuota, 
                dc.tiempo_cuota, 
                dc.num_cuotas ultima_cuota, 
                dc.total_pagar 
            FROM 
                tbl_deudores_creditos_cuotas cc 
                INNER JOIN tbl_deudores_creditos dc 
                ON dc.codigo_credito = cc.codigo_credito 
            WHERE 
                cc.codigo_cuota = $codigo_cuota";

    $res = $conexion->ejecutar_sql($sql);

    if ($res->num_rows > 0) {
        $reg = $res->fetch_assoc();

        $fecha_pago = $reg["fecha_pago"];
        $codigo_credito = $reg["codigo_credito"];
        $total_pagar = $reg["total_pagar"];
        $valor_pago = $reg["valor_pago"];
        $numero_cuota = $reg["numero_cuota"];
        $fecha_hoy = date('Y-m-d');

        // Obtener tasa de interés
        $tasa_interes = $conexion->consultar_campo("tbl_deudores_creditos", "codigo_tasa_interes", "codigo_credito='$codigo_credito'");
        $tasa_interes_mensual = floatval($tasa_interes[0]);
        $tasa_diaria = $tasa_interes_mensual / 30 /100;

        // Calcular días de atraso
        $dias_diferencia = (int)date_diff(date_create($fecha_hoy), date_create($fecha_pago))->format('%R%a');

        // Verificar si hay una siguiente cuota para limitar el rango del atraso
        $sql_max = "SELECT MAX(numero_cuota) as ultima_cuota FROM tbl_deudores_creditos_cuotas WHERE codigo_credito='$codigo_credito'";
        $res_max = $conexion->ejecutar_sql($sql_max);
        $ultima_cuota = 0;
        if ($res_max->num_rows > 0) {
            $reg_max = $res_max->fetch_assoc();
            $ultima_cuota = $reg_max["ultima_cuota"];
        }

        // Limitar días de atraso si hay cuota siguiente
        if ($numero_cuota < $ultima_cuota) {
            $sql_siguiente = "SELECT fecha_pago FROM tbl_deudores_creditos_cuotas 
                              WHERE codigo_credito='$codigo_credito' AND numero_cuota=".($numero_cuota+1);
            $res_sig = $conexion->ejecutar_sql($sql_siguiente);
            if ($res_sig->num_rows > 0) {
                $reg_sig = $res_sig->fetch_assoc();
                $fecha_limite = $reg_sig["fecha_pago"];
                if ($fecha_hoy > $fecha_limite) {
                    $dias_diferencia = (int)date_diff(date_create($fecha_limite), date_create($fecha_pago))->format('%R%a');
                }
            }
        }

        // Calcular interés de mora solo si hay atraso
        $valor_incremento = 0;
        $dias_mora = $dias_diferencia < 0 ? abs($dias_diferencia) : 0;

        if ($dias_mora > 0) {
            // El interés de mora se aplica sobre la cuota vencida
            $valor_incremento = ($valor_pago * $tasa_diaria) * $dias_mora;
        }

        $respuesta = [
            "success" => true,
            "fecha_pago" => $fecha_pago,
            "tasa_interes_mensual" => $tasa_interes_mensual,
            "dias_atraso" => $dias_mora,
            "valor_pago" => $valor_pago,
            "valor_incremento" => round($valor_incremento, 0)
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($respuesta);
