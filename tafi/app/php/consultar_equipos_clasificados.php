
<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");


    function consultar_tabla_posiciones($args){
        $conexion=new conexion_db(); 
        $retorno=array();

        $codigo_grupo="";
        $codigo_ronda="";
        $codigo_torneo=$args["codigo_torneo"];

        if(isset($args["codigo_grupo"])){
            $codigo_grupo=$args["codigo_grupo"];
        }

        if(isset($args["codigo_ronda"])){
            $codigo_ronda=$args["codigo_ronda"];
        }

        $orden=orden_clasificacion_torneo($codigo_torneo);

        $sql="SELECT 
		eq.codigo_equipo,
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

        if(codigo_clasificacion =1 ,    fn_numero_tarjetas_torneo('A',tq.codigo_torneo,eq.codigo_equipo),
        fn_numero_tarjetas_torneo_rondas('A',tq.codigo_torneo,eq.codigo_equipo,".$codigo_ronda.")) AS AMARILLAS,

        if(codigo_clasificacion =1 ,    fn_numero_tarjetas_torneo('A',tq.codigo_torneo,eq.codigo_equipo),
        fn_numero_tarjetas_torneo_rondas('R',tq.codigo_torneo,eq.codigo_equipo,".$codigo_ronda.")) AS ROJAS
        
        
        FROM tbl_tafi_equipos eq 
        
        INNER JOIN tbl_tafi_torneos_equipos tq ON 
        eq.codigo_equipo=tq.codigo_equipo

        INNER JOIN tbl_tafi_torneos tor ON 
        tq.codigo_torneo=tor.codigo_torneo
        
        INNER JOIN tbl_tafi_torneos_calendario cal ON 
        tq.codigo_equipo IN  (cal.codigo_local,cal.codigo_visitante)
        AND cal.codigo_estado=3 ";

        if($codigo_grupo!=""){
            $sql.=" AND cal.codigo_grupo='".$codigo_grupo."'";
        }
        
        $sql.="INNER join tbl_tafi_torneos_calendario_fechas f on 
        cal.codigo_fecha=f.codigo_fecha ";
        
        if($codigo_ronda!=""){
            $sql.=" AND f.codigo_ronda='".$codigo_ronda."' ";
        }
        
        $sql.=" WHERE tq.codigo_torneo='".$codigo_torneo."'
        
        GROUP BY eq.codigo_equipo
        
        ORDER BY " . $orden . ", eq.nombre_equipo ASC";
       
        $resultado=$conexion->ejecutar_sql($sql);

        if($resultado->num_rows > 0){
            $resultado = $resultado->fetch_all(MYSQLI_ASSOC);
            
    
            $retorno["resultado"]=1;
            $retorno["datos"]= $resultado;
        }else{
            $retorno["resultado"]=0;
            $retorno["mensaje"]="No se cargo la clasificacion del torneo";
        }

        return $retorno;

    }


    function consultar_equipos_clasificados($codigo_torneo, $cantidad_equipos_clasifican, $codigo_ronda, $codigo_clasificacion){
        $conexion=new conexion_db(); 
        $retorno=array();

        $orden=orden_clasificacion_torneo($codigo_torneo);


        if($codigo_clasificacion==1){
            $sql="SELECT 
            eq.codigo_equipo,
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
            
            ORDER BY " . $orden . ", eq.nombre_equipo ASC
            
            LIMIT ". $cantidad_equipos_clasifican;

            $resultado=$conexion->ejecutar_sql($sql);
        
            if($resultado->num_rows > 0){
                $resultado = $resultado->fetch_all(MYSQLI_ASSOC);
        
                $retorno["resultado"]=1;
                $retorno["datos"]= $resultado;
            }else{
                $retorno["resultado"]=0;
                $retorno["mensaje"]="No se cargo la clasificacion del torneo";
            }

        }else{
            //SE CONSULTAN LOS GRUPOS DEL TORNEO
            $sql="SELECT 

            g.codigo_grupo
            
            FROM tbl_tafi_torneos_calendario_fechas f 
            
            INNER JOIN tbl_tafi_torneos_calendario_fechas_grupos g ON 
            f.codigo_grupo=g.codigo_grupo
            
            WHERE f.codigo_torneo=6
            AND f.codigo_ronda=1
            
            GROUP BY g.codigo_grupo";

            $resultado_g=$conexion->ejecutar_sql($sql);

            if($resultado_g->num_rows > 0){
                $datos_grupos = $resultado_g->fetch_all(MYSQLI_ASSOC);
             
                $retorno["resultado"]=1;
                
                $clasificacion_grupo=array();

                $i=0;
                foreach($datos_grupos as $key=>$value){
                    //SE CONSULTA LA TABLA DE POSICIONES DE CADA GRUPO

                    $sql="SELECT 
                        eq.codigo_equipo,
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
                        
                        INNER JOIN tbl_tafi_torneos_calendario cal ON 
                        tq.codigo_equipo IN  (cal.codigo_local,cal.codigo_visitante)
                        AND cal.codigo_estado=3

                        INNER JOIN tbl_tafi_torneos_calendario_fechas f ON
                        cal.codigo_fecha=f.codigo_fecha
                        
                        WHERE tq.codigo_torneo='".$codigo_torneo."'
                        and cal.codigo_grupo='".$value["codigo_grupo"]."'
                        AND f.codigo_ronda='".$codigo_ronda."'
                        
                        GROUP BY eq.codigo_equipo
                        
                        ORDER BY " . $orden . ", eq.nombre_equipo ASC
                        
                        LIMIT ". $cantidad_equipos_clasifican;

                    $resultado=$conexion->ejecutar_sql($sql);

                    if($resultado->num_rows > 0){
                        $resultado = $resultado->fetch_all(MYSQLI_ASSOC);

                        foreach ($resultado as $key_eq => $value_eq) {
                            $clasificacion_grupo[$i]=$value_eq;
                            $i++;
                        }
                        
                    }

                }

               
                $retorno["datos"]= $clasificacion_grupo;
                
            }else{
                $retorno["resultado"]=0;
                $retorno["mensaje"]="No se cargo la clasificacion del torneo";
            }
        }
        
        return $retorno;
    }

    function orden_clasificacion_torneo($codigo_torneo){
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

        return $orden;
    }

?>