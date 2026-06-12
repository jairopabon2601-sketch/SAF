<?php
 
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
        
    $conexion=new conexion_db();
    $retorno=array();

    $codigo_torneo=$_POST["codigo_torneo"];

    $sql="SELECT 

    eq.nombre_equipo, 
    format(TRUNCATE(if(SUM(ab.valor_abono) IS NULL, '0', SUM(ab.valor_abono)),0),0) AS pagado,
    format(TRUNCATE((cost.valor - if(SUM(ab.valor_abono) IS NULL, 0, SUM(ab.valor_abono))),0),0)  AS saldo

    FROM tbl_tafi_torneos t 
    
    INNER JOIN tbl_tafi_torneos_equipos te ON 
    t.codigo_torneo=te.codigo_torneo
    
    INNER JOIN tbl_tafi_equipos eq ON 
    te.codigo_equipo=eq.codigo_equipo
    
    LEFT JOIN tbl_tafi_torneos_inscripcion_abonos ab ON 
    ab.codigo_torneo=t.codigo_torneo
    AND ab.codigo_equipo=eq.codigo_equipo
    
    INNER JOIN tbl_tafi_torneos_costos cost ON 
    t.codigo_torneo=cost.codigo_torneo
    AND cost.codigo_concepto=1
    
    WHERE t.codigo_torneo='".$codigo_torneo."'
    
    GROUP BY eq.codigo_equipo
    
    ORDER BY eq.nombre_equipo";

    $resultado=$conexion->ejecutar_sql($sql);

    if($resultado->num_rows>0){
        $retorno["resultado"]=1;

        $datos=$resultado->fetch_all(MYSQLI_ASSOC);
        $retorno["datos_inscipcion"]=$datos;
        
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargaron las inscripciones";
    }
    
    echo json_encode($retorno);

?>