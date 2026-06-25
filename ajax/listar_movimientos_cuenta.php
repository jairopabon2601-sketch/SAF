<?php
session_start();
require_once("../conexion/conexion.php");

$conexion = new conexion_db();

$usuario = isset($_SESSION['codigo_usuario']) ? $_SESSION['codigo_usuario'] : null;
if (!$usuario) {
    header('Content-Type: application/json');
    echo json_encode([
        'movimientos' => [],
        'total_paginas' => 1,
        'pagina_actual' => 1
    ]);
    exit;
}

$codigo_cuenta = isset($_REQUEST['codigo_cuenta']) ? intval($_REQUEST['codigo_cuenta']) : 0;
$pagina = isset($_REQUEST['pagina']) ? max(1, intval($_REQUEST['pagina'])) : 1;
$desde = isset($_REQUEST['desde']) && $_REQUEST['desde'] !== '' ? $_REQUEST['desde'] : null;
$hasta = isset($_REQUEST['hasta']) && $_REQUEST['hasta'] !== '' ? $_REQUEST['hasta'] : null;
$por_pagina = 200;
$offset = ($pagina - 1) * $por_pagina;

$where = "codigo_cuenta='$codigo_cuenta' AND usuario='$usuario' AND estado!=2";
if ($desde && $hasta) {
    $where .= " AND fecha BETWEEN '$desde' AND '$hasta'";
}

$total = $conexion->consultar_campo('tbl_cuentas_movimientos', 'COUNT(*)', $where);
$total = $total ? intval($total[0]) : 0;
$total_paginas = max(1, ceil($total / $por_pagina));

$sql = "SELECT m.codigo, m.fecha, t.nombre AS tipo_movimiento, m.valor, m.descripcion
        FROM tbl_cuentas_movimientos m
        LEFT JOIN tbl_cuentas_tipo t ON m.tipo_movimiento = t.codigo
        WHERE $where ORDER BY m.fecha DESC, m.codigo DESC LIMIT $offset, $por_pagina";

echo $sql;
$res = $conexion->ejecutar_sql($sql);
$movimientos = [];
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $movimientos[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode([
    'movimientos' => $movimientos,
    'total_paginas' => $total_paginas,
    'pagina_actual' => $pagina
]);