<?php
session_start();
require_once("../conexion/conexion.php");
$conexion = new conexion_db();

$cuenta_codigo = intval($_POST['cuenta_codigo']);
$tipo_movimiento = $_POST['tipo_movimiento'];
$valor = floatval($_POST['valor']);
$fecha = $_POST['fecha'];
$descripcion = trim($_POST['descripcion']);
$usuario = isset($_SESSION['codigo_usuario']) ? $_SESSION['codigo_usuario'] : null;

$campos = array(
    'codigo_cuenta' => $cuenta_codigo,
    'tipo_movimiento' => $tipo_movimiento,
    'valor' => $valor,
    'fecha' => $fecha,
    'descripcion' => $descripcion,
    'usuario' => $usuario
);
$res = $conexion->insertar('tbl_cuentas_movimientos', $campos);

if ($res) {
    echo json_encode(['success' => true, 'msg' => 'Movimiento registrado correctamente.']);
} else {
    echo json_encode(['success' => false, 'msg' => 'Error al registrar el movimiento.']);
}