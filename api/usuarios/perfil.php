<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once("../../php/usuarios.php");

$usuario_obj = new usuarios();

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// 🔐 Validar Token
$payload = $usuario_obj->validarToken();

// Limpiar respuesta para Flutter
$exp = $payload['exp'];
unset($payload['exp']);

echo json_encode([
    "status" => "success",
    "mensaje" => "Acceso correcto",
    "usuario" => $payload,
    "expiracion_token" => date("Y-m-d H:i:s", $exp)
]);
