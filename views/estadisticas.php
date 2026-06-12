<style type="text/css">
.ul_menu_opciones_estadisticas{
		list-style:none;
		margin:0;
		padding:0;
		width: 100%;
		margin-bottom: 15px;
	}

	.ul_menu_opciones_estadisticas li{
		text-decoration: none;
		display: inline-block;
		border: 1px solid #efefef;
		border-radius: 8px;
		padding: 4px;
		background-color:#eee;
		box-shadow: 0 1px 10px rgba(0,0,0,0.1);
		font-size: 20px;		
	}

	.ul_menu_opciones_estadisticas li a{
		text-align: center;
	}

	.ul_menu_opciones_estadisticas .seleccionado{
		background-color:#191970;
		font-weight: bold;
		color: #fff;
	}

    #div_listado table{
        display: inline-table;
    }

@media (max-width: 600px) {
    table.dataTable th, table.dataTable td {
        padding: 4px 2px;
        font-size: 12px;
    }
    .ul_menu_opciones_estadisticas li {
        font-size: 14px;
        padding: 2px;
    }
}
</style>
<!-- Incluye jQuery y DataTables correctamente -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
	var valor_diario;
	$(document).ready(function() {
		cargar_listado('puntuales');

		// Tabs
		$(".ul_menu_opciones_estadisticas li").on("click", function() {
			$(".ul_menu_opciones_estadisticas li").removeClass("seleccionado");
			$(this).addClass("seleccionado");
			var categoria = $(this).data("categoria");
			mostrar_tab_categoria(categoria);
			cargar_listado(categoria);
		});

		// Mostrar solo el primer tab al inicio
		mostrar_tab_categoria('puntuales');

		$("#btn_crear_usu").bind("click",cargar_select_dialog);

		$("#codigo_tipo_usuario").on("change",function(){
			res=consultar_campo("tbl_usuarios_tipos","tabla_origen,campo_tabla_origen","codigo_tipo_usuario=1");
			resultado=res.split(";");

			tabla_origen=resultado[0];
			campo_tabla=resultado[1];

			console.log(tabla_origen); 

			rellenar_select(tabla_origen,campo_tabla,"concat(nombres,' ',apellidos)","","codigo_origen");

		});

	});

	function diferenciaDias(fecha_desde, fecha_hasta){
		let fecha1 = new Date(fecha_desde);
		let fecha2 = new Date(fecha_hasta);
		let diferencia = fecha2.getTime() - fecha1.getTime();
		let diasDeDiferencia = diferencia / 1000 / 60 / 60 / 24;
		return diasDeDiferencia; 
	}

	function cargar_listado(categoria) {
		switch(categoria) {
			case 'puntuales':
				listado_consulta("div_puntuales","listado_clientes_puntuales","");
				break;
			case 'mas_creditos':
				listado_consulta("div_mas_creditos","listado_clientes_mas_creditos","");
				break;
			case 'mayor_monto':
				listado_consulta("div_mayor_monto","listado_clientes_mayor_monto","");
				break;
			case 'antiguedad':
				listado_consulta("div_antiguedad","listado_clientes_antiguedad","");
				break;
			case 'retrasos':
				listado_consulta("div_retrasos","listado_clientes_retrasos","");
				break;
			case 'nuevos':
				listado_consulta("div_nuevos","listado_clientes_nuevos","");
				break;
			case 'mejor_scoring':
				//listado_consulta("div_mejor_scoring","listado_clientes_mejor_scoring","");
				break;
			case 'anticipados':
				listado_consulta("div_anticipados","listado_clientes_anticipados","");
				break;
		}
	}

	function consulta_datos(nombre_consulta, table_id, columnas, filtro) {
		// Si el id no empieza con #, lo agrega automáticamente
		if (table_id.charAt(0) !== '#') {
			table_id = '#' + table_id;
		}
		$.ajax({
			type: 'POST',
			async: true,
			url: 'ajax/listado_json_campos.php',
			data: {
				codigo_consulta: nombre_consulta,
				filtro: filtro,
				agrupacion: ""
			},
			success: function (data) {
				if (!data.datos || data.datos.length === 0) {
					$(table_id + ' tbody').empty();
					return;
				}
				// Depuración: verifica que DataTable esté disponible y el selector sea correcto
				console.log('table_id:', table_id, 'DataTable:', $.fn.DataTable);
				if (!$.fn.DataTable) {
					alert('DataTable no está cargado.');
					return;
				}
				if ($(table_id).length === 0) {
					alert('No existe la tabla con el selector: ' + table_id);
					return;
				}
				// Inicializa DataTable si no existe
				if (!$.fn.DataTable.isDataTable(table_id)) {
					// Evita clonar el thead dos veces
					if ($(table_id + ' thead tr').length < 2) {
						$(table_id + ' thead tr').clone(true).appendTo(table_id + ' thead');
					}
					var table = $(table_id).DataTable({
						orderCellsTop: true,
						fixedHeader: true,
						paging: true,
						searching: true,
						ordering: true,
						order: [], // Mantener el orden del backend
						responsive: true // Activar responsive
					});
					$(table_id + ' thead tr:eq(1) th').each(function(i) {
						$('input', this).on('keyup change', function() {
							if (table.column(i).search() !== this.value) {
								table
									.column(i)
									.search(this.value)
									.draw();
							}
						});
					});
				}
				var table = $(table_id).DataTable();
				table.clear();
				data.datos.forEach(function(row) {
					let rowData = columnas.map(col => row[col]);
					table.row.add(rowData);
				});
				table.draw();
			},
			dataType: 'json'
		});
	}

	function mostrar_tab_categoria(categoria) {
		$(".div_categoria").hide();
		$("#div_" + categoria).show();
	}

	function cargar_select_dialog(){
		rellenar_select("tbl_perfiles","codigo_perfil","nombre","","codigo_perfil");

		rellenar_select("tbl_usuarios_tipos","codigo_tipo_usuario","nombre","","codigo_tipo_usuario");
	}

	// Ejemplo de uso para el tab de Mejor Scoring
	$(document).ready(function() {
		$('li[data-categoria="mejor_scoring"]').on('click', function() {
			mostrar_tab_categoria('mejor_scoring'); // Primero muestra el tab
			setTimeout(function() {
				consulta_datos(
					'listado_mejor_scoring',
					'tabla_mejor_scoring',
					['cliente', 'cantidad_creditos', 'puntaje_total', 'indice_promedio'],
					""
				);
			}, 100); // Espera 100 ms para asegurar que el DOM se actualice
		});
	});

</script>

<div style="display: flex;">
	<div style="width: 50%"><h3>Estadísticas</h3></div>
</div>
<hr>
<div id="menu_tabs">
    <ul class="ul_menu_opciones_estadisticas">
        <li style="width: 20%;" data-categoria="puntuales" class="seleccionado">Pagan Puntual</li>
        <li style="width: 20%;" data-categoria="mas_creditos">Más Créditos</li>
        <li style="width: 20%;" data-categoria="mayor_monto">Mayor Monto</li>
        <li style="width: 20%;" data-categoria="antiguedad">Mayor Antigüedad</li>
        <li style="width: 20%;" data-categoria="retrasos">Más Retrasos</li>
        <li style="width: 20%;" data-categoria="nuevos">Nuevos Clientes</li>
        <li style="width: 20%;" data-categoria="mejor_scoring">Mejor Scoring</li>
        <li style="width: 20%;" data-categoria="anticipados">Pagos Anticipados</li>
    </ul>
    <div id="div_puntuales" class="div_categoria"><div id="div_listado"></div></div>
    <div id="div_mas_creditos" class="div_categoria" style="display:none;"></div>
    <div id="div_mayor_monto" class="div_categoria" style="display:none;"></div>
    <div id="div_antiguedad" class="div_categoria" style="display:none;"></div>
    <div id="div_retrasos" class="div_categoria" style="display:none;"></div>
    <div id="div_nuevos" class="div_categoria" style="display:none;"></div>
    <div id="div_mejor_scoring" class="div_categoria" style="display:none;">
        <div id="tabla_mejor_scoring_wrapper" style="overflow-x:auto;">
            <table id="tabla_mejor_scoring" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Cantidad de Créditos</th>
                        <th>Puntaje Total</th>
                        <th>Índice Promedio</th>
                    </tr>
                    <tr>
                        <th><input type="text" placeholder="Buscar cliente" /></th>
                        <th><input type="text" placeholder="Filtrar cantidad" /></th>
                        <th><input type="text" placeholder="Filtrar puntaje" /></th>
                        <th><input type="text" placeholder="Filtrar índice" /></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llenará por JS -->
                </tbody>
            </table>
        </div>
    </div>
    <div id="div_anticipados" class="div_categoria" style="display:none;"></div>
</div>  

