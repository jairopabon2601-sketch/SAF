<?php
	
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/conexion/conexion.php");
	if (isset($_POST)){
		listado_consulta($_POST["consulta"],$_POST["filtro"],$_POST["opcion"]);
	}

	function listado_consulta($codigo_consulta,$filtro_campo,$opcion){


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
				$columnas=$datos_columnas[0];

				$value_campos_ocultos=array();

				$tabla="<table class='table table-striped table-responsive'>";
					$tabla.="<thead><tr>";
						$i=0;
						foreach ($columnas as $key => $value) {
							if($i>=$res[0]["campos_ocultos"]){
								$tabla.="<td>" . $key . "</td>";
							}
							$i++;
						}

						if ($opcion==1) {
							$tabla.="<td>Opciones</td>";
						}

					$tabla.="</tr></thead>";


					$tabla.="<tbody>";

						$x=0;
						foreach ($datos_consulta as $value) {

			
							$tabla.="<tr ";

							if (isset($datos_columnas[$x]["color"])) {
								$tabla.="style='background:".$datos_columnas[$x]["color"]."'";
							}

							$tabla.=" >";
								$i=0;
								foreach ($value as $valor) {
									if ($i < $num_campos_ocultos) {

										$tabla.="<input type='hidden' name='oculto".$i."[]' value='".$value[$i]."'>  ";
										$pabon = $value[$i];
									}else{
										$tabla.="<td>" . $value[$i] ."</td>";
									}
									$i++;
								}
								
								//OPCIONES
								$sw_opciones=0;
								if ($opcion==1) {
									$tabla.="<td>";

									$sql="SELECT * FROM tbl_conf_consultas_opciones WHERE codigo_consulta='".$codigo_consulta."' AND codigo_estado=1";
									$resul=$conexion->ejecutar_sql($sql);
									
									if ($resul->num_rows>0) {
										$datos_consulta_opciones=$resul->fetch_all(MYSQLI_ASSOC);

											foreach ($datos_consulta_opciones as $value_op) {
												$sw=1;

												if ($value_op["criterio_consulta"]!="") {
														$sw = $_SESSION["codigo_perfil"]!=6 ? validar_criterio($value_op["criterio_consulta"],$datos_columnas[$x]) : 1;
														
												}

												if($sw==1){
													if ($value_op["tipo"]==1) {
		
														$tabla.="<a class='hover' title='".$value_op["titulo"]."' onclick='".$value_op["funcion_ejecutar"]."'><img src='".$value_op["icono"]."' style='width: 30px;'></a>";
														$sw_opciones=1;		
													}else{
														$tabla.="<a class='hover' title='".$value_op["titulo"]."' onclick='abrir_url(&quot;".$value_op["url"]."&quot;)'><img src='".$value_op["icono"]."' style='width: 30px;'></a>";
													}
												}
												
													

												
											}

										
									}

									
									$o=0;	
									foreach ($datos_columnas[$x] as $key => $val) {

										if ($o < $num_campos_ocultos) {
											$tabla=str_replace("<<".$key.">>",$val,$tabla);
										}

										$o++;	
									}			
												

									$tabla.="</td>";
								}
							$tabla.="</tr>";

							$x++;
						}

						
					$tabla.="</tbody>";


				$tabla.="</table>";
										
		
				$retorno["resultado"]=1;
				$retorno["mensaje"]=$tabla;

			}else{
				$retorno["resultado"]=0;
				$retorno["mensaje"]="No se encontraron registros de la consulta.";
			}

		}

		echo json_encode($retorno);

	}

	function validar_criterio($criterio_consulta, $reg){

		$co=$criterio_consulta;

		foreach($reg as $campo=>$valor){
			if(!(is_numeric($campo))){
				$co=str_replace($campo, $valor, $co);
			}
		}

		$co=str_replace("=", "==", $co);
		eval("\$res_co=(" . $co . ");");

		if($res_co){
			$sw=1;
		}else{
			$sw=0;									
		}

		return $sw;

	}

?>