<?php
require_once("../conexion/conexion.php");
$conexion = new conexion_db();
$codigo_cuenta = intval($_REQUEST['codigo_cuenta']);
$pagina = isset($_REQUEST['pagina']) ? max(1, intval($_REQUEST['pagina'])) : 1;
//$usuario = $_REQUEST['usuario'];
$desde = isset($_REQUEST['desde']) && $_REQUEST['desde'] !== '' ? $_REQUEST['desde'] : date('Y-m-d', strtotime('-1 month'));
$hasta = isset($_REQUEST['hasta']) && $_REQUEST['hasta'] !== '' ? $_REQUEST['hasta'] : date('Y-m-d');
$por_pagina = 50;
$offset = ($pagina - 1) * $por_pagina;

$where = "codigo_cuenta='$codigo_cuenta' AND estado!=2 AND tipo_movimiento=2";
if ($desde && $hasta) {
    $where .= " AND fecha BETWEEN '$desde' AND '$hasta'";
}

$total = $conexion->consultar_campo('tbl_cuentas_movimientos', 'COUNT(*)', $where);
$total = $total ? intval($total[0]) : 0;
$total_paginas = max(1, ceil($total / $por_pagina));

$sql = "SELECT m.codigo, 
        date_format(m.fecha, '%d/%m/%Y') AS fecha,
        if(m.tipo_movimiento=2,'Gasto','Ingreso') AS tipo_movimiento,
        m.valor, m.descripcion
        FROM tbl_cuentas_movimientos m
        WHERE $where ORDER BY m.fecha DESC, m.codigo DESC LIMIT $offset, $por_pagina";
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