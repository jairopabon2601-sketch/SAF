<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once("../../php/usuarios.php");

$usuario_obj = new usuarios();

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// 🔐 Validar Token (solo usuarios logueados pueden registrar otros)
$auth = $usuario_obj->validarToken();

// Leer JSON
$json = file_get_contents("php://input");
$data = json_decode($json, true);

// Validar que venga información
if (!$data) {
    echo json_encode([
        "error" => "No se recibieron datos"
    ]);
    exit;
}

$resultado = $usuario_obj->registrar_usuario($data);

echo json_encode([
    "resultado" => $resultado
]);
