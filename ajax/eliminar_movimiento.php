<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

$con = new conexion_db();

try {
    
    // Validar que se reciba el código del movimiento
    if (!isset($_POST['codigo_movimiento'])) {
        throw new Exception('Código de movimiento no proporcionado');
    }

    $codigo_movimiento = $_POST['codigo_movimiento'];

    // Obtener los datos del movimiento antes de eliminarlo
    $sql_movimiento = "SELECT codigo_cuenta, tipo_movimiento, valor FROM tbl_cuentas_movimientos WHERE codigo = '$codigo_movimiento'";
    
    $result_movimiento = $con->ejecutar_sql($sql_movimiento);
    
    if (!$result_movimiento || $result_movimiento->num_rows == 0) {
        throw new Exception('Movimiento no encontrado');
    }

    $movimiento = $result_movimiento->fetch_assoc();
    $codigo_cuenta = $movimiento['codigo_cuenta'];
    $tipo_movimiento = $movimiento['tipo_movimiento'];
    $valor = floatval($movimiento['valor']);

    // Calcular el ajuste del saldo
    // Si es gasto (tipo_movimiento = 2), sumar al saldo (porque se está eliminando un gasto)
    // Si es ingreso (tipo_movimiento = 3), restar al saldo (porque se está eliminando un ingreso)
    $factor = $tipo_movimiento == 2  ? 1 : -1;
    $ajuste_saldo = $valor * $factor;

    // Iniciar transacción para asegurar consistencia
    $con->ejecutar_sql("START TRANSACTION");

    try {
        // 1. Eliminar el movimiento cambiando estado a 2
        $campos_movimiento = array('estado' => 2);
        $filtro_movimiento = "codigo = '$codigo_movimiento'";
        
        $resultado_eliminar = $con->actualizar("tbl_cuentas_movimientos", $campos_movimiento, $filtro_movimiento);
        
        if (!$resultado_eliminar) {
            throw new Exception('Error al eliminar el movimiento');
        }

        // 2. Obtener el saldo actual de la cuenta
        $sql_saldo = "SELECT saldo_actual FROM tbl_cuentas WHERE codigo = '$codigo_cuenta'";
        $result_saldo = $con->ejecutar_sql($sql_saldo);
        
        if (!$result_saldo || $result_saldo->num_rows == 0) {
            throw new Exception('Cuenta no encontrada');
        }

        $cuenta = $result_saldo->fetch_assoc();
        $saldo_actual = floatval($cuenta['saldo_actual']);
        $nuevo_saldo = $saldo_actual + $ajuste_saldo;

        // 3. Actualizar el saldo de la cuenta
        $campos_cuenta = array('saldo_actual' => $nuevo_saldo);
        $filtro_cuenta = "codigo = '$codigo_cuenta'";
        
        $resultado_saldo = $con->actualizar("tbl_cuentas", $campos_cuenta, $filtro_cuenta);
        
        if (!$resultado_saldo) {
            throw new Exception('Error al actualizar el saldo de la cuenta');
        }

        // Confirmar transacción
        $con->ejecutar_sql("COMMIT");

        $retorno["resultado"] = 1;
        $retorno["mensaje"] = "Movimiento eliminado con éxito y saldo actualizado.";
        $retorno["ajuste_saldo"] = $ajuste_saldo;
        $retorno["nuevo_saldo"] = $nuevo_saldo;

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $con->ejecutar_sql("ROLLBACK");
        throw $e;
    }

} catch (Exception $e) {
    $retorno["resultado"] = 0;
    $retorno["mensaje"] = $e->getMessage();
}

// Debug: ver qué se está devolviendo
error_log("Respuesta JSON: " . json_encode($retorno));

echo json_encode($retorno);
?>
