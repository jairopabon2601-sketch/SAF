<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once("../../php/usuarios.php");

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Detectar si viene por POST (JSON) o Form-Data
$input = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $input ? $input : $_POST;
} else {
    // Solo para pruebas rápidas por GET
    $data = [
        "email" => $_GET['email'] ?? '',
        "password" => $_GET['password'] ?? ''
    ];
}

$email = $data['email'] ?? '';
$pass = $data['password'] ?? '';

if (empty($email) || empty($pass)) {
    echo json_encode(["resultado" => 0, "mensaje" => "Email y password requeridos"]);
    exit;
}

$usuario = new usuarios();
$resultado = $usuario->login($email, $pass);

echo json_encode($resultado);
