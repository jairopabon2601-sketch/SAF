<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

	$conexion=new conexion_db();
	$filtro = $_POST["filtro"];

	$retorno=array();

	$res=$conexion->buscar("tbl_tafi_conf_consultas","nombre_consulta='".$_POST["consulta"]."'");

	if (empty($res)) {
		$retorno["resultado"]=0;
		$retorno["mensaje"]="No se encontró la consulta!";
	}else{
		$sql=$res[0]["consulta"];
		$sql=str_replace("<<filtro>>",$filtro,$res[0]["consulta"]);
		$res_consulta=$conexion->ejecutar_sql($sql); 

		if ($res_consulta->num_rows>0) {
				
			$datos_consulta=$res_consulta->fetch_all(MYSQLI_NUM);

			$datos_columnas=$conexion->ejecutar_sql($sql);		
			$datos_columnas=$datos_columnas->fetch_all(MYSQLI_ASSOC);	
			
			$columnas=$datos_columnas[0];

			$tabla="<table class='table table-striped table-responsive' style='display: inline-table;'>";
						$j=0;
						foreach ($columnas as $key => $val) {
							if($j<$res[0]["campos_ocultos"]){
								$tabla.="<input type='hidden' id='".$key."' name='".$key."' value='".$val."'>";
							}
							$j++;
						}
				$tabla.="<tbody>";
				
					foreach ($datos_consulta as $value) {
							$i=0;
							foreach ($columnas as $key => $value2) {
								//print_r($key);
								if($i>=$res[0]["campos_ocultos"]){
									if($i<=count($columnas) - 1){
										$tabla.="<tr><td>". $key."</td><td>" . $value[$i] . "</td></tr>";
									}
								}
								$i++;
							}
					}

				$tabla.="</tbody></table>";

				

			$value_campos_ocultos=array();

			$retorno["datos"]=$tabla;
			$retorno["resultado"]=1;

		}else{
			$retorno["resultado"]=0;
			$retorno["mensaje"]="No se encontraron registros de la consulta.";
		}

	}

	echo json_encode($retorno);
?>