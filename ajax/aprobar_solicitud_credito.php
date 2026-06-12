<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/conexion/conexion.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/ajax/enviar_mail.php");

$conexion = new conexion_db();
$retorno = "";
$resultado = 1;

$codigo_solicitud = $_POST["codigo_solicitud"];
$estado_solicitud = $_POST["codigo_estado"];
$observacion = $_POST["observacion"];

$campos = array();
$campos["codigo_estado"] = $estado_solicitud;
$campos["observacion"] = $observacion;
$campos["fecha_aprobacion"] = date("Y-m-d");
$res = $conexion->actualizar("tbl_deudores_creditos_solicitudes", $campos, "codigo_solicitud=" . $codigo_solicitud);
if ($res) {

    $sql = "SELECT * FROM tbl_deudores_creditos_solicitudes WHERE codigo_solicitud = $codigo_solicitud";

    $result = $conexion->ejecutar_sql($sql);
    $registros = $result->fetch_all(MYSQLI_ASSOC);
    $reg = $registros[0];

    $asunto = $estado_solicitud == 1 ? "Solicitud de Crédito Aprobada" : "Solicitud de Crédito Rechazada";
    $mensaje = "<h1>SAF : Respuesta a solicitud de Crédito</h1>";
    $mensaje .= "<p>Estimado(a) " . $reg["nombres"] . " " . $reg["apellidos"] . "</p>";




    $campos = array();
    $campos["codigo_asesor"] = $reg["codigo_asesor"];
    $campos["num_documento"] = $reg["documento"];
    $campos["nombres"] = $reg["nombres"];
    $campos["apellidos"] = $reg["apellidos"];
    $campos["direccion"] = $reg["direccion"];
    $campos["telefono"] = $reg["telefono"];


    if ($result->num_rows > 0) {

        $datos = $conexion->consultar_campo("tbl_deudores", "codigo_deudor,count(*)", "num_documento = '" . $reg["documento"] . "'");
        if ($datos[1] > 0) {
            $codigo_deudor = $datos[0];
            $res = $conexion->actualizar("tbl_deudores", $campos, "codigo_deudor=" . $codigo_deudor);
        } else {
            $res = $conexion->insertar("tbl_deudores", $campos);
            $codigo_deudor = $conexion->insert_id();
        }

        if ($res || $codigo_deudor > 0) {

            $datos_credito_anterior = $conexion->consultar_campo("tbl_deudores_creditos", "count(*)", "codigo_deudor = $codigo_deudor AND codigo_estado = 1");
            $credito_anterior = $datos_credito_anterior[0];
            if ($estado_solicitud == 1 && $credito_anterior == 0) {
                $num_cuotas = $reg["num_cuotas"];
                $tiempo_cuota = $reg["tiempo_cuota"];
                $codigo_tasa_interes = $reg["codigo_tasa_interes"];
                $valor_prestamo = $reg["valor_prestamo"];
                $fecha_aprobacion = $reg["fecha_aprobacion"];
                //$tasa_interes = $conexion->consultar_campo("tbl_tasa_interes", "valor", "codigo_tasa_interes=" . $codigo_tasa_interes);

                $campos = array();
                $campos["codigo_deudor"] = $codigo_deudor;
                $campos["fecha_prestamo"] = $fecha_aprobacion;
                $campos["valor_prestamo"] = $valor_prestamo;
                $total_pagar = intval(($valor_prestamo * $num_cuotas) / $tiempo_cuota * $codigo_tasa_interes) / 100;
                $total_pagar = $total_pagar + intval($valor_prestamo);
                $campos["total_pagar"] = $total_pagar;
                $campos["tiempo_cuota"] = $tiempo_cuota;
                $campos["num_cuotas"] = $num_cuotas;
                $campos["codigo_tasa_interes"] = $codigo_tasa_interes;
                $campos["tipo_interes"] = isset($reg["tipo_interes"]) ? $reg["tipo_interes"] : 1;
                $res = $conexion->insertar("tbl_deudores_creditos", $campos);

                $codigo_credito = $conexion->insert_id();
                $tipo_interes = isset($reg["tipo_interes"]) ? $reg["tipo_interes"] : 1;
                $tasa_decimal = $codigo_tasa_interes / 100;
                $saldo_restante = $valor_prestamo;

                for ($i = 1; $i <= $num_cuotas; $i++) {
                    switch ($tiempo_cuota) {
                        case '2':
                            //FRECUENCIA QUINCENAL						
                            $unidad = "DAY";
                            $tiempo = 15;
                            break;
                        case '1':
                            $unidad = "MONTH";
                            $tiempo = 1;
                            break;
                        case '3':
                            $unidad = "DAY";
                            $tiempo = 7;
                            break;
                    }



                    $fecha_aprobacion = date("Y-m-d", strtotime($fecha_aprobacion . " + " . $tiempo . "" . $unidad));

                    // Calcular valor de cuota según tipo de interés
                    if ($tipo_interes == 1) {
                        // Interés Fijo - valor constante
                        $valor_pago = round($total_pagar / $num_cuotas);
                    } else {
                        // Interés Variable - calcular sobre saldo restante
                        $interes_cuota = $saldo_restante * $tasa_decimal;
                        $amortizacion = $valor_prestamo / $num_cuotas;
                        $valor_pago = round($amortizacion + $interes_cuota);
                        $saldo_restante -= $amortizacion;
                    }

                    $campos = array();
                    $campos["codigo_credito"] = $codigo_credito;
                    $campos["fecha_pago"] = $fecha_aprobacion;
                    $campos["numero_cuota"] = $i;
                    $campos["valor_pago"] = $valor_pago;

                    $res = $conexion->insertar("tbl_deudores_creditos_cuotas", $campos);

                    if ($res == false) {
                        $retorno = "Error al grabar cuota: " . $i;
                    }
                }

                $sql = "SELECT 
                    cc.numero_cuota AS 'No. Cuota',
                    DATE_FORMAT(cc.fecha_pago, '%d-%b-%Y') AS 'Fecha para pago',
                    formato_dinero(cc.valor_pago) AS 'Valor a Pagar'
                    
                    FROM tbl_deudores_creditos_cuotas cc
                    
                    LEFT JOIN tbl_usuarios u 
                    ON u.codigo_usuario = cc.usuario_registro_pago
                    
                    WHERE cc.codigo_credito =" . $codigo_credito . "
                    
                    GROUP BY cc.codigo_cuota
                    
                    ORDER BY cc.fecha_pago";
                $result = $conexion->ejecutar_sql($sql);
                $registros = $result->fetch_all(MYSQLI_ASSOC);
                $reg_cuota = $registros[0];
                $mensaje .= "<p><b>FELICITACIONES.<b> Su solicitud de crédito ha sido aprobada</p>";
                $mensaje .= "<p>El desembolso de tu crédito por valor : <b>$" . number_format($valor_prestamo, 0) . "</b> se realizará en  las siguientes <b> 24 Hrs.</b></p>";
                $mensaje .= "<div style='margin: 10px;'>";
                $mensaje .= "<table border='1' style='border-collapse: collapse;'>";
                $mensaje .= "<tr style='background: #0069b5;color: white;'>";
                $mensaje .= "<th style='padding:5px;'>No. Cuota</th>";
                $mensaje .= "<th style='padding:5px;'>Fecha para pago</th>";
                $mensaje .= "<th style='padding:5px;'>Valor a Pagar</th>";
                $mensaje .= "</tr>";
                $color_impar = "#F8F9FC";
                $color_par = "#EBECEF";
                $color = $color_impar;
                foreach ($registros as $reg_cuotas) {
                    $mensaje .= "<tr style='background: $color;'>";
                    $mensaje .= "<td style='padding:5px;'>" . $reg_cuotas["No. Cuota"] . "</td>";
                    $mensaje .= "<td style='padding:5px;'>" . $reg_cuotas["Fecha para pago"] . "</td>";
                    $mensaje .= "<td style='padding:5px;'>" . $reg_cuotas["Valor a Pagar"] . "</td>";
                    $mensaje .= "</tr>";
                    if ($color == $color_impar) {
                        $color = $color_par;
                    } else {
                        $color = $color_impar;
                    }
                }
                $mensaje .= "</table>";
            } else {
                $campos = array();
                $campos["codigo_estado"] = 2;
                $campos["observacion"] = "El cliente ya tiene un crédito activo";
                $conexion->actualizar("tbl_deudores_creditos_solicitudes", array("codigo_estado" => 2), "codigo_solicitud=" . $reg["codigo_solicitud"]);
                $retorno = "Solicitud Rechazada.";
                $resultado = 0;
                $asunto = "Solicitud de Crédito Rechazada";
                $mensaje .= "<p><b>LO SENTIMOS.</b> Su solicitud de crédito ha sido rechazada</p>";
            }
            // Agregar la observación al mensaje si existe
            if (!empty($observacion)) {
                $mensaje .= "<p><strong>Observación:</strong> " . $observacion . "</p>";
            }
        } else {
            $retorno = "Error al grabar deudor.<br>" . $conexion->error($res);
            $resultado = 0;
        }
    } else {
        $retorno = "No existe registros";
        $resultado = 0;
    }

    // Enviar correo al solicitante usando el email de la solicitud
    if (!empty($reg["email"])) {
        error_log("SAF - Preparando envío de correo a: " . $reg["email"]);

        $resultado_envio = enviar_mail($reg["email"], $asunto, $mensaje);

        if ($resultado_envio) {
            error_log("SAF - Correo enviado exitosamente al solicitante: " . $reg["email"]);
        } else {
            error_log("SAF - Error al enviar correo al solicitante: " . $reg["email"]);
            $retorno .= " - Error al enviar correo al solicitante";
        }
    } else {
        error_log("SAF - No se encontró email en la solicitud: " . $codigo_solicitud);
        $retorno .= " - No se encontró email en la solicitud";
    }

    // Enviar copia al administrador
    error_log("SAF - Enviando copia al administrador");
    $resultado_admin = enviar_mail('jairojesuspabonsuarez@gmail.com', $asunto, $mensaje);

    if ($resultado_admin) {
        error_log("SAF - Copia enviada exitosamente al administrador");
    } else {
        error_log("SAF - Error al enviar copia al administrador");
    }
} else {
    $retorno = "Error en la aprobación de la solicitud.<br>" . $conexion->error($res);
    $resultado = 0;
}


$retorno = $retorno == "" ? "Datos registrado con éxito" : $retorno;

$datos = array("resultado" => $resultado, "mensaje" => $retorno);
echo json_encode($datos);

?>