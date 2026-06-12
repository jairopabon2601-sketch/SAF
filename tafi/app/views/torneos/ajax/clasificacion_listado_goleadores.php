<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    
$conexion=new conexion_db();

$datos=array();

$sql="SELECT 

CONCAT('archivos/jugadores/fotos/',jug.ruta_foto) AS foto_jugador,
UCASE(CONCAT(jug.nombres,' ',jug.apellidos)) AS jugador,
jug.numero_documento,
tq.nombre_equipo, 
CONCAT('archivos/equipos/escudos/',tq.escudo) AS escudo,
SUM(cal_res.numero_goles) AS goles, 
COUNT(DISTINCT cal_res.codigo_resultado) AS cantidad_partidos

FROM tbl_tafi_torneos_calendario c 

INNER JOIN tbl_tafi_torneos_calendario_resultados cal_res ON 
c.codigo_calendario=cal_res.codigo_calendario

INNER join tbl_tafi_jugadores jug ON 
cal_res.codigo_jugador=jug.codigo_jugador

INNER JOIN tbl_tafi_equipos tq ON 
cal_res.codigo_equipo=tq.codigo_equipo

WHERE c.codigo_torneo='".$_POST["codigo_torneo"]."'

GROUP BY cal_res.codigo_jugador

ORDER BY goles DESC, cantidad_partidos ASC 

LIMIT 10";

$resultado=$conexion->ejecutar_sql($sql);

if($resultado->num_rows>0){
    $datos=$resultado->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($datos);

?>