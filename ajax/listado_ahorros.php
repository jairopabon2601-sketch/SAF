<?php
	
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");

	$conexion=new conexion_db();

	$retorn=array();
	$retorno["meses_pagos"]="";
	$retorno["mensaje"]="";
	$retorno["resultado"]="";
	$retorno["ahorradores"]="";
	$retorno["meses_ahorro"]="";

	$sql="SELECT 

		am.*,
		CONCAT(mes.abreviatura,'-',YEAR(am.fecha_cuota)) AS 'nombre_mes',
		IF(anio.tipo=1,SUM(if(cou.estado=0,0,aho.valor_pactado)),SUM(if(cou.estado=0,0,cou.valor_pagado))) AS 'total_mes'

		FROM tbl_ahorro_anyos anio

		INNER JOIN tbl_ahorro_anyos_meses am ON 
		anio.codigo_ahorro_anyo=am.codigo_ahorro_anyo

		LEFT JOIN tbl_ahorradores_cuotas cou ON 
		am.codigo_ahorro_mes=cou.codigo_ahorro_mes

		LEFT JOIN tbl_ahorradores_ahorros  aho ON 
		cou.codigo_ahorro=aho.codigo_ahorro

		INNER JOIN tbl_items_meses mes ON 
		am.codigo_mes=mes.codigo_mes

		WHERE anio.anyos='".$_POST["anio_ahorro"]."'

		GROUP BY am.codigo_ahorro_mes

		ORDER BY am.orden_cuota";

	$resultado=$conexion->ejecutar_sql($sql);
			  
	if($resultado->num_rows > 0){

		$retorno["resultado"]=1;
		$meses_ahorro = $resultado->fetch_all(MYSQLI_ASSOC);
		$retorno["meses_ahorro"]=$meses_ahorro; 


		$sql='SELECT 

			datos.*,
			if(MONTH(datos.Fecha_ingreso) > datos.codigo_mes ,
			ROUND((((datos.porcentaje / 12 ) * datos.meses_ahorratados/100) * (datos.total_ahorrado)) + (datos.total_ahorrado)) 
			, datos.total_ahorrado + datos.total_ahorrado * datos.porcentaje /100 ) AS "neto_pagar" 

			FROM( 
			SELECT 
			a.codigo_ahorro, 
			UCASE(CONCAT(ah.nombres," ",ah.apellidos)) AS "ahorrador", 
			ase.sigla AS "asesor", 
			a.fecha_ingreso AS "Fecha_ingreso", 
			a.valor_pactado AS "Valor_pactado",
			ah_anio.tipo,
			IF(ah_anio.tipo=1,SUM(if(cuo.estado=0,0,a.valor_pactado)),SUM(if(cuo.estado=0,0,cuo.valor_pagado))) AS "total_ahorrado",
			por_ren.porcentaje,
			por_ren.codigo_mes,
			SUM(if(cuo.estado=0,0,1)) AS "meses_ahorratados",
			min(ah_mes.orden_cuota) as "orden_cuota"

			FROM tbl_ahorradores_ahorros a
			
			INNER JOIN tbl_ahorro_anyos ah_anio ON 
			a.codigo_ahorro_anyo=ah_anio.codigo_ahorro_anyo

			INNER JOIN tbl_ahorradores ah ON 
			a.codigo_ahorrador=ah.codigo

			INNER JOIN tbl_asesores ase ON 
			ah.codigo_asesor=ase.codigo_asesor

			left JOIN tbl_porcentaje_rendimiento por_ren ON 
			por_ren.anio=ah_anio.anyos

			inner JOIN tbl_ahorradores_cuotas cuo ON 
			a.codigo_ahorro=cuo.codigo_ahorro

			INNER JOIN tbl_ahorro_anyos_meses ah_mes on 
			cuo.codigo_ahorro_mes=ah_mes.codigo_ahorro_mes

			WHERE ah_anio.anyos="'.$_POST["anio_ahorro"].'"

			GROUP BY a.codigo_ahorro

			ORDER BY ase.sigla) AS datos';
			 
			//print_r($sql);
		$resultado=$conexion->ejecutar_sql($sql);

				
		if($resultado->num_rows > 0){
			$retorno["resultado"]=1;
			$resultado_ahorradores = $resultado->fetch_all(MYSQLI_ASSOC);
			$retorno["ahorradores"]=$resultado_ahorradores;
			
			$i=0;	
			foreach ($resultado_ahorradores as $value) {
				$sql="SELECT 
				c.*,
				if(c.estado=0 AND DAYOFMONTH(CURDATE()) > 5 AND c.fecha_cuota <= CURDATE(),'No Pago',if(c.estado=0,'$ 0','Pagado') ) AS 'estado_pago',
				if(c.estado=0 AND (DAYOFMONTH(CURDATE()) > 5 or c.fecha_cuota <= CURDATE()) ,'1','0') AS 'mora',
				if(c.estado=0 AND (DAYOFMONTH(CURDATE()) > 5 or c.fecha_cuota <= CURDATE()) ,'#F87070','') AS 'color',
				mes.nombre AS 'nombre_mes',
				ase.sigla,
				aho_mes.codigo_ahorro_mes,
				aho_mes.orden_cuota

				FROM tbl_ahorradores_cuotas c 

				INNER JOIN tbl_ahorro_anyos_meses aho_mes ON 
				c.codigo_ahorro_mes=aho_mes.codigo_ahorro_mes

				LEFT JOIN tbl_usuarios usu ON 
				usu.codigo_usuario=c.usuario_registro_pago

				LEFT JOIN tbl_asesores ase ON 
				usu.codigo_origen=ase.codigo_asesor

				INNER JOIN tbl_items_meses mes ON 
				c.codigo_mes=mes.codigo_mes

				WHERE c.codigo_ahorro='".$value["codigo_ahorro"]."'

				ORDER BY aho_mes.orden_cuota";		

				$resultado=$conexion->ejecutar_sql($sql);
				
				if($resultado->num_rows > 0){
					$ahorros_cuotas = $resultado->fetch_all(MYSQLI_ASSOC);
			
					$retorno["ahorradores"][$i]["ahorros"]=$ahorros_cuotas; 
				}

				$i++;	
			}	

			
		}else{
			$retorno["mensaje"]="No se configuran las cuotas para este año.";
			$retorno["resultado"]=0;
		}

	}else{
		$retorno["mensaje"]="No se encontraron ahorradores en: ". $_POST["anio_ahorro"];
		$retorno["resultado"]=0;
	}

	echo json_encode($retorno);

?>