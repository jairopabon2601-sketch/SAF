<?php
require_once("../conexion/conexion.php");
$conexion = new conexion_db();
$sql = "SELECT c.codigo, c.nombre, c.color, c.estado, t.nombre AS tipo_nombre, c.saldo_actual
        FROM tbl_cuentas c
        LEFT JOIN tbl_cuentas_tipo t ON c.tipo = t.codigo
        ORDER BY c.codigo DESC";
$res = $conexion->ejecutar_sql($sql);
$cuentas = [];
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        
        $cuentas[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($cuentas); 