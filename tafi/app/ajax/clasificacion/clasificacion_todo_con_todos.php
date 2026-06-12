<?php

    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

    $codigo_torneo=$_POST["codigo_torneo"];
    
    $conexion=new conexion_db(); 
    $retorno=array();

    //SE CONSULTA EL ORDER BY DE LA CLASIFICACION
    $sql="SELECT 
    item.campo_orden AS campo_orden 
    FROM tbl_tafi_torneos_clasificacion_items it

    INNER JOIN tbl_tafi_torneos_tipos_clasificacion_items item ON 
    it.codigo_item=item.codigo_item_clasificacion

    WHERE it.codigo_torneo='".$codigo_torneo."'
    AND item.campo_orden!='0'

    GROUP BY  it.codigo_item

    ORDER BY it.orden";

    $resultado=$conexion->ejecutar_sql($sql);

    if($resultado->num_rows > 0){
        $resultado = $resultado->fetch_all(MYSQLI_ASSOC);

        $orden="";

        foreach($resultado as $key=>$value){
            $orden.=$value["campo_orden"];
            if($key<count($resultado)-1){
                $orden.=",";
            }
        }
    }else{
        $orden="PTS DESC, DIF DESC, GF DESC";
    }


    $sql="SELECT 

    eq.escudo,
    eq.nombre_equipo,
    (SUM(
    CASE WHEN 
    (cal.codigo_local = tq.codigo_equipo AND cal.resultado_local > cal.resultado_visitante ) OR 
    ( cal.codigo_visitante = tq.codigo_equipo AND cal.resultado_visitante > cal.resultado_local) 
    THEN 3 ELSE 0 END
    ) + 
    SUM(CASE WHEN ( cal.codigo_local = tq.codigo_equipo OR cal.codigo_visitante =  tq.codigo_equipo ) 
    
    AND ( cal.resultado_local = cal.resultado_visitante ) THEN 1 ELSE 0 END)) AS PTS, 
    
    SUM(CASE WHEN ( cal.codigo_local = tq.codigo_equipo OR cal.codigo_visitante = tq.codigo_equipo ) THEN 1 ELSE 0 END) AS PJ,
    
    SUM(CASE WHEN ( cal.codigo_local = tq.codigo_equipo AND cal.resultado_local > cal.resultado_visitante ) 
    OR ( cal.codigo_visitante = tq.codigo_equipo AND cal.resultado_visitante > cal.resultado_local ) THEN 1 ELSE 0 END) AS V , 
    
    SUM(CASE WHEN ( cal.codigo_local = tq.codigo_equipo OR cal.codigo_visitante = tq.codigo_equipo ) AND cal.resultado_local = cal.resultado_visitante THEN 1 ELSE 0 END) AS E , 
    
    SUM(CASE WHEN ( cal.codigo_local = tq.codigo_equipo AND cal.resultado_local < cal.resultado_visitante ) 
    OR ( cal.codigo_visitante = tq.codigo_equipo AND cal.resultado_visitante < cal.resultado_local ) THEN 1 ELSE 0 END) AS D ,  
    
    COALESCE(SUM(
    CASE 
    WHEN (cal.codigo_local = tq.codigo_equipo) THEN cal.resultado_local 
    WHEN (cal.codigo_visitante = tq.codigo_equipo) THEN cal.resultado_visitante END),0) AS GF, 
    
    COALESCE(SUM(CASE 
    WHEN (cal.codigo_local = tq.codigo_equipo) THEN cal.resultado_visitante 
    WHEN (cal.codigo_visitante = tq.codigo_equipo) THEN cal.resultado_local END),0) AS GC,

    COALESCE((SUM(CASE WHEN (cal.codigo_local = tq.codigo_equipo) THEN cal.resultado_local 
    WHEN (cal.codigo_visitante = tq.codigo_equipo) THEN cal.resultado_visitante END) - 

    SUM(CASE WHEN (cal.codigo_local = tq.codigo_equipo) THEN cal.resultado_visitante WHEN (cal.codigo_visitante = tq.codigo_equipo) THEN cal.resultado_local END)),0) AS DIF,

    fn_numero_tarjetas_torneo('A',tq.codigo_torneo,eq.codigo_equipo) AS AMARILLAS,
    fn_numero_tarjetas_torneo('R',tq.codigo_torneo,eq.codigo_equipo) AS ROJAS
    
    FROM tbl_tafi_equipos eq 
    
    INNER JOIN tbl_tafi_torneos_equipos tq ON 
    eq.codigo_equipo=tq.codigo_equipo
    
    LEFT JOIN tbl_tafi_torneos_calendario cal ON 
    tq.codigo_equipo IN  (cal.codigo_local,cal.codigo_visitante)
    AND cal.codigo_estado=3
    
    WHERE tq.codigo_torneo='".$codigo_torneo."'
    
    GROUP BY eq.codigo_equipo
    
    ORDER BY " . $orden . ", eq.nombre_equipo ASC";

	$resultado=$conexion->ejecutar_sql($sql);
	
	if($resultado->num_rows > 0){
        $resultado = $resultado->fetch_all(MYSQLI_ASSOC);

        $retorno["resultado"]=1;
        $retorno["datos"]=$resultado;
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se cargo la clasificacion del torneo";
    }

    echo json_encode($retorno);
?>