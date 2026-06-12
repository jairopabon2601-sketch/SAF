<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Ajustar rutas según la estructura: /api/listado_consultas.php
require_once($_SERVER['DOCUMENT_ROOT'] . "/conexion/conexion.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/php/usuarios.php");


// 1. Validar Token de Seguridad
$usuario_obj = new usuarios();

// Manejo de preflight (OPTIONS) para Flutter/Web
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$auth = $usuario_obj->validarToken(); // 🔐 Si falla, el método termina la ejecución con error JSON

// 2. Recibir Parámetros
// Aceptamos tanto JSON como Form-Data
$input = json_decode(file_get_contents("php://input"), true);
$codigo_consulta = $_POST["consulta"] ?? $input["consulta"] ?? "";
$filtro_campo = $_POST["filtro"] ?? $input["filtro"] ?? "1";

if (empty($codigo_consulta)) {
    echo json_encode([
        "status" => "error",
        "mensaje" => "Debe especificar el código de la consulta (ej: listado_creditos)"
    ]);
    exit;
}

$conexion = new conexion_db();

// 3. Obtener definición de la consulta desde la base de datos
// Se usa la misma lógica que el sistema web actual
$res = $conexion->buscar("tbl_conf_consultas", "nombre_consulta='" . $codigo_consulta . "'");

if (empty($res)) {
    echo json_encode([
        "status" => "error",
        "mensaje" => "No se encontró la configuración para la consulta: " . $codigo_consulta
    ]);
    exit;
}

// 4. Preparar y Ejecutar el SQL
$filtro = empty($filtro_campo) ? "1" : $filtro_campo;
$sql = str_replace("<<filtro>>", $filtro, $res[0]["consulta"]);

try {
    $res_consulta = $conexion->ejecutar_sql($sql);
    
    if ($res_consulta && $res_consulta->num_rows > 0) {
        $datos = $res_consulta->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            "status" => "success",
            "resultado" => 1,
            "cantidad" => count($datos),
            "consulta_nombre" => $codigo_consulta,
            "datos" => $datos
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "resultado" => 0,
            "mensaje" => "No se encontraron registros para esta consulta o filtro.",
            "datos" => []
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "mensaje" => "Error en la base de datos: " . $e->getMessage()
    ]);
}
