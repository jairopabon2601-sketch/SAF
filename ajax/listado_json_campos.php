<?php
	
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");
	if (isset($_POST)){
		listado_json_campos($_POST["codigo_consulta"],$_POST["filtro"]);
	}

	function listado_json_campos($codigo_consulta,$filtro_campo){

		$conexion=new conexion_db();
		
		$res=$conexion->buscar("tbl_conf_consultas","nombre_consulta='".$codigo_consulta."'");
		

		if (empty($res)) {
			$retorno["resultado"]=0;
			$retorno["mensaje"]="No se encontró la consulta!";
		}else{

			$num_campos_ocultos=$res[0]["campos_ocultos"];

			if (empty($_POST["filtro"])) {
				$filtro=1;
			}else{
				$filtro=$filtro_campo;
			}

			$consulta=str_replace("<<filtro>>",$filtro,$res[0]["consulta"]);
			
			$res_consulta=$conexion->ejecutar_sql($consulta);

			if ($res_consulta->num_rows>0) {
					
				$datos_consulta=$res_consulta->fetch_all(MYSQLI_NUM);

				$datos_columnas=$conexion->ejecutar_sql($consulta);		
				$datos_columnas=$datos_columnas->fetch_all(MYSQLI_ASSOC);	
				
										
		
				$retorno["resultado"]=1;
				$retorno["datos"]=$datos_columnas;

			}else{
				$retorno["resultado"]=0;
				$retorno["datos"]="No se encontraron registros de la consulta.";
			}

		}

		echo json_encode($retorno);

	}

?>