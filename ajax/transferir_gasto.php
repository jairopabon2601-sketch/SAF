<?php
session_start();
require_once '../conexion/conexion.php';

header('Content-Type: application/json');

try {
    // Crear instancia de conexión
    $conexion = new conexion_db();
    
    // Validar datos de entrada
    if (!isset($_POST['cuenta_origen']) || !isset($_POST['cuenta_destino']) || 
        !isset($_POST['valor']) || !isset($_POST['fecha'])) {
        throw new Exception('Faltan datos requeridos');
    }

    $cuenta_origen = $_POST['cuenta_origen'];
    $cuenta_destino = $_POST['cuenta_destino'];
    $valor = floatval($_POST['valor']);
    $fecha = $_POST['fecha'];
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

    // Validaciones
    if ($cuenta_origen == $cuenta_destino) {
        throw new Exception('No puede transferir a la misma cuenta');
    }

    if ($valor <= 0) {
        throw new Exception('El valor debe ser mayor a 0');
    }

    // Verificar que las cuentas existan y estén activas
    $sql_origen = "SELECT codigo, nombre, saldo_actual, estado FROM tbl_cuentas WHERE codigo = '$cuenta_origen' AND estado = 1";
    $resultado_origen = $conexion->ejecutar_sql($sql_origen);
    if (!$resultado_origen || $resultado_origen->num_rows == 0) {
        throw new Exception('La cuenta origen no existe o está inactiva');
    }
    $cuenta_origen_data = $resultado_origen->fetch_assoc();

    $sql_destino = "SELECT codigo, nombre, saldo_actual, estado FROM tbl_cuentas WHERE codigo = '$cuenta_destino' AND estado = 1";
    $resultado_destino = $conexion->ejecutar_sql($sql_destino);
    if (!$resultado_destino || $resultado_destino->num_rows == 0) {
        throw new Exception('La cuenta destino no existe o está inactiva');
    }
    $cuenta_destino_data = $resultado_destino->fetch_assoc();
    

    // Verificar saldo suficiente en cuenta origen
    $saldo_origen = $cuenta_origen_data['saldo_actual'] ?? 0;
    if ($saldo_origen < $valor) {
        throw new Exception('Saldo insuficiente en la cuenta origen. Saldo disponible: $' . number_format($saldo_origen, 0, ',', '.'));
    }

    // Iniciar transacción
        // Obtener tipos de movimiento (gasto e ingreso)
        

        // Si no encuentra los tipos específicos, usar códigos por defecto (2=gasto, 3=ingreso)
        $tipo_gasto = 2;
        $tipo_ingreso = 3;

        // Registrar movimiento de gasto en cuenta origen
        $descripcion_gasto = $descripcion ? "Transferencia a " . $cuenta_destino_data['nombre'] . ": " . $descripcion : "Transferencia a " . $cuenta_destino_data['nombre'];
        
        $sql_insert_gasto = "INSERT INTO tbl_cuentas_movimientos (codigo_cuenta, tipo_movimiento, valor, fecha, descripcion) VALUES ('$cuenta_origen', '$tipo_gasto', '$valor', '$fecha', '$descripcion_gasto')";
        $resultado_insert_gasto = $conexion->ejecutar_sql($sql_insert_gasto);
        if (!$resultado_insert_gasto) {
            throw new Exception('Error al registrar movimiento de gasto: ' . $conexion->error());
        }

        // Registrar movimiento de ingreso en cuenta destino
        $descripcion_ingreso = $descripcion ? "Transferencia desde " . $cuenta_origen_data['nombre'] . ": " . $descripcion : "Transferencia desde " . $cuenta_origen_data['nombre'];
        
        $sql_insert_ingreso = "INSERT INTO tbl_cuentas_movimientos (codigo_cuenta, tipo_movimiento, valor, fecha, descripcion) VALUES ('$cuenta_destino', '$tipo_ingreso', '$valor', '$fecha', '$descripcion_ingreso')";
        $resultado_insert_ingreso = $conexion->ejecutar_sql($sql_insert_ingreso);
        if (!$resultado_insert_ingreso) {
            throw new Exception('Error al registrar movimiento de ingreso: ' . $conexion->error());
        }

        echo json_encode([
            'success' => true,
            'msg' => 'Transferencia realizada exitosamente. Se registraron los movimientos correspondientes en ambas cuentas.'
        ]);


} catch (Exception $e) {
    // Log del error para depuración
    error_log("Error en transferir_gasto.php: " . $e->getMessage() . " - Linea: " . $e->getLine());
    
    echo json_encode([
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]
    ]);
}
?> 