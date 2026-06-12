<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $conexion=new conexion_db();

    $sql="SELECT 
    
    eq.escudo,
    tor.nombre_torneo,
    eq.nombre_equipo,
    jug.dorsal AS numero
    
    FROM tbl_tafi_equipos_jugadores jug 
    
    INNER JOIN tbl_tafi_equipos eq ON 
    jug.codigo_equipo=eq.codigo_equipo
    
    INNER JOIN tbl_tafi_torneos_equipos tq ON 
    eq.codigo_equipo=tq.codigo_equipo
    
    INNER JOIN tbl_tafi_torneos tor ON 
    tq.codigo_torneo=tor.codigo_torneo
    
    WHERE jug.codigo_jugador='".$_POST["codigo_jugador"]."'
    
    GROUP BY tor.codigo_torneo, eq.codigo_equipo";

	$resultado=$conexion->ejecutar_sql($sql);
	
	if($resultado->num_rows > 0){
        $resultado = $resultado->fetch_all(MYSQLI_ASSOC);

        $retorno["resultado"]=1;
        $retorno["datos"]= $resultado;
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargo la trayectoria del jugador";
    }

    echo json_encode($retorno);
?>  