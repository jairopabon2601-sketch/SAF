<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/form_windows_creditos.php');
?>


<style type="text/css">
	::-webkit-scrollbar {
		width: 5px;
	}

	::-webkit-scrollbar-track {
		-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
		border-radius: 10px;
	}

	::-webkit-scrollbar-thumb {
		border-radius: 10px;
		-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
	}

	.modal-content {
		width: 130%;
		height: 100%;
		overflow: auto;
	}

	.form-filtro {
		border: 1px solid #d1d3e2;
		border-top-left-radius: 0;
		border-bottom-left-radius: 0;
		color: #6e707e;
		width: 50%;
	}

	.slidecontainer {
		width: 70%;
		margin: 5%;
	}

	.slider {
		-webkit-appearance: override;
		width: 100%;
		height: 15px;
		border-radius: 5px;
		background: #d3d3d3;
		outline: none;
		opacity: 0.7;
		-webkit-transition: .2s;
		transition: opacity .2s;
	}

	.slider:hover {
		opacity: 1;
	}

	.slider::-webkit-slider-thumb {
		-webkit-appearance: none;
		appearance: none;
		width: 25px;
		height: 25px;
		border-radius: 50%;
		background: #47478D;
		cursor: pointer;
	}

	.slider::-moz-range-thumb {
		width: 25px;
		height: 25px;
		border-radius: 50%;
		background: #47478D;
		cursor: pointer;
	}
	
	.div_texto_oscuro * {
		color: black !important;
	}
</style>
<script src="js/jquery.growl.js" type="text/javascript"></script>
<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />
<link href="css/tablas_creditos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	var eq_ahorro = 0;

	<?php
	echo "var codigo_perfil='" . $_SESSION["codigo_perfil"] . "';\n";
	echo "var codigo_asesor='" . $_SESSION["codigo_origen"] . "';\n";
	?>


	$(document).ready(function () {
		$("#btn_crear_deudor").bind("click", agregar_deudor);
		$("#btn_grabar_deudor").bind("click", grabar_deudor);
		$("#btn_editar_solicitud").bind("click", grabar_edicion_solicitud);
		$("#btn_aprobar_solicitud").bind("click", grabar_aprobacion);
		$("#btn_consultar").bind("click", cargar_listado_creditos);
		$('#btn_crear_credito').on('click', function () {

			datos_deudor();
			datos_tasa();
			$('#reg_creditos')[0].reset();
			// Llenar selects de fuente
			rellenar_select("tbl_cuentas", "codigo", "nombre", "", "fuente_credito_reg", "");
		});
		$('#interes').bind('change', function () {
			if ($(this).val() == 2) {
				tiempo = consultar_campo("tbl_deudores_creditos", "", $("#codigo_tasa").val(), "tiempo");
				valor_interes = consultar_campo("tbl_deudores_creditos", "((valor_prestamo*codigo_tasa_interes)/100)/tiempo_cuota", "codigo_credito='" + $("#modal_coutas_creditos").attr('rel') + "'");
				$('#valor_pagado').val(valor_interes);
			} else {
				$('#valor_pagado').val('');
			}
		});
		cargar_listado_creditos();
		$("#btn_grabar_credito").bind("click", grabar_credito);
		$("#btn_grabar_cuota").bind("click", grabar_cuota_credito);
		rellenar_select("tbl_asesores", "codigo_asesor", "concat(nombres,' ',apellidos)", "", "codigo_asesor_filtro", "");
		rellenar_select("tbl_deudores_creditos_estados", "codigo", "nombre", "", "codigo_estado_filtro", "");
		rellenar_select("tbl_asesores", "codigo_asesor", "concat(nombres,' ',apellidos)", "", "form_edicion_solicitud select#codigo_asesor", "");
		rellenar_select("tbl_tasa_interes", "valor", "concat(valor,'%')", "", "form_edicion_solicitud select#codigo_tasa_interes", "", "codigo_tasa_interes");
		// Llenar selects de fuente
		rellenar_select("tbl_cuentas", "codigo", "nombre", "", "fuente_credito_edit", "");
		rellenar_select("tbl_cuentas", "codigo", "nombre", "", "fuente_cuota", "");
		

		$('#codigo_asesor_filtro').bind('change', function () { cargar_listado_creditos(); });
		$('#codigo_estado_filtro').bind('change', function () { cargar_listado_creditos(); });

		$('#codigo_tasa_interes_reg').bind('change', function () {
			cargar_total_credito_reg();
		});

		$('#tipo_interes').bind('change', function () {
			cargar_total_credito_reg();
		});

		$("#fecha_hasta").on("change",function(){
			diferencia_dias=diferenciaDias($("#fecha_desde").val(),$("#fecha_hasta").val())
			valor_total = formatNumber(parseInt(diferencia_dias*valor_diario));
			console.log(valor_total);
			$("#valor_cuota").text(valor_total);

		});

		 //tabs
		 $("#btn_scoring").bind("click", function(){
				$(this).parent().find("li").removeClass("seleccionado");
				$(this).addClass("seleccionado");
				$("#div_scoring").show();
				$("#div_simulador").hide();
				return false;
		});
        $("#btn_simulador").bind("click", function(){
                $(this).parent().find("li").removeClass("seleccionado");
                $(this).addClass("seleccionado");
                $("#div_scoring").hide();
                $("#div_simulador").show();
                return false;
        });

		$("#rango_meses").on("input",function(){
			$("#meses").html($(this).val());
			cargar_total();
		});

		$("#rango_monto").on("input",function(){
			$("#monto").html(formatNumber($(this).val()));
			$("#rango_monto_valor").val($(this).val());
			cargar_total();
		});

		$("#rango_interes").on("input",function(){
			$("#interes_sim").html($(this).val());
			cargar_total();
		});

		$("#fecha_hasta").on("change",function(){
			cargar_total();
		});
	});




	function cargar_listado_creditos() {

		union_fl = "";
		filtro = "";
		if (codigo_perfil == '6') {
			$(".admin").show();

			if ($('#codigo_asesor_filtro').val() > 0) {
				filtro += union_fl + " d.codigo_asesor='" + $('#codigo_asesor_filtro').val() + "'";
				union_fl = " and ";
			}

		} else {

			filtro += union_fl + 'd.codigo_asesor="' + codigo_asesor + '"';
			union_fl = ' and ';

		}
		if ($('#codigo_estado_filtro').val() > 0) {

			filtro += union_fl + 'c.codigo_estado="' + $('#codigo_estado_filtro').val() + '"';
			union_fl = ' and ';

		}
		console.log(filtro);
		listado_consulta("div_listado_creditos", "listado_creditos", filtro, 1);
		listado_consulta("div_listado_creditos_pendientes", "listado_creditos_solicitados", filtro, 1);
		consulta_datos(filtro);
	}

	function consulta_datos(filtro) {
		$.ajax({
			type: 'POST',
			async: true,
			url: 'ajax/listado_json_campos.php',
			data: {
				codigo_consulta: "json_total_creditos_valores",
				filtro: filtro,
				agrupacion: ""
			},
			success: function (data) {
				$('#spn_pagado').text(data["datos"][0].pagado);
				$('#spn_pendiente').text(data["datos"][0].pendiente);
			},
			dataType: 'json'
		});
	}


	function agregar_deudor() {
		rellenar_select("tbl_asesores", "codigo_asesor", "concat(nombres,' ',apellidos)", "", "codigo_asesor", "");
		$('#reg_usuarios')[0].reset();

	}

	function cargar_total_credito_reg() {
		valor_prestamo = $("#valor_prestamo").val();
		num_cuotas = $("#num_cuotas").val();
		tasa_sim = $("#codigo_tasa_interes_reg").val();
		tiempo_cuotas = $("#tiempo_cuota").val();
		fecha_prestamo = $("input[name='fecha_prestamo']").val();
		tipo_interes = $("#tipo_interes").val();

		if (!valor_prestamo || !num_cuotas || !tasa_sim || !tiempo_cuotas || !fecha_prestamo) {
			$('#tabla_proyeccion_cuotas').html('').hide();
			$("#total_pagar").val("");
			return;
		}

		var tasa_decimal = tasa_sim / 100;
		
		if (tipo_interes == 1) {
			// Interés Fijo - cálculo actual
			total_pagar = parseInt(valor_prestamo) + ((parseInt(valor_prestamo) * tasa_decimal / tiempo_cuotas)* parseInt(num_cuotas));
		} else {
			// Interés Variable - calcular total sumando todas las cuotas
			var saldo_restante = parseInt(valor_prestamo);
			var amortizacion = saldo_restante / parseInt(num_cuotas);
			var total_interes = 0;
			
			for (var i = 1; i <= parseInt(num_cuotas); i++) {
				var interes_cuota = saldo_restante * tasa_decimal;
				total_interes += interes_cuota;
				saldo_restante -= amortizacion;
			}
			
			total_pagar = parseInt(valor_prestamo) + total_interes;
		}

		function formatearMoneda(valor) {
			let redondeado = Math.ceil(valor / 100) * 100;
			return redondeado.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });
		}
		$("#total_pagar").val(formatearMoneda(total_pagar));

		// Llamada AJAX para proyección de cuotas
		$.ajax({
			url: 'ajax/proyeccion_cuotas.php',
			method: 'POST',
			data: {
				fecha_prestamo: fecha_prestamo,
				valor_prestamo: valor_prestamo,
				num_cuotas: num_cuotas,
				tiempo_cuota: tiempo_cuotas,
				tasa: tasa_sim,
				total_pagar: total_pagar,
				tipo_interes: $("#tipo_interes").val()
			},
			dataType: 'json',
			success: function(respuesta) {
				var tabla = '<div class="table-responsive"><table class="table table-striped table-hover table-bordered" style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px #0001;">';
				tabla += '<thead class="thead-dark"><tr><th style="background:#47478D;color:white;">Fecha</th><th style="background:#47478D;color:white;">Valor</th></tr></thead><tbody>';
				respuesta.forEach(function(cuota) {
					tabla += '<tr><td>' + cuota.fecha + '</td><td>' + formatearMoneda(cuota.valor) + '</td></tr>';
				});
				tabla += '</tbody></table></div>';
				$('#tabla_proyeccion_cuotas').html(tabla).show();
			}
		});
	}

	function datos_deudor() {
		rellenar_select("tbl_deudores", "codigo_deudor", "concat(nombres,' ',apellidos)", "", "codigo_deudor", "", "nombres");
	}

	function datos_tasa() {
		rellenar_select("tbl_tasa_interes", "valor", "concat(valor,'%')", "", "codigo_tasa_interes_reg", "", "codigo_tasa_interes");
	}

	function ver_cuotas(codigo_credito) {
		$("#modal_listado_coutas_creditos").modal();
		$("#modal_coutas_creditos").attr('rel', codigo_credito);
		filtro = "cc.codigo_credito='" + codigo_credito + "'";
		listado_consulta("div_listado_cuotas_creditos", "listado_cuotas_creditos", filtro, 1);

	}

	function editar_solicitud(codigo_solicitud) {
		document.form_edicion_solicitud.reset();
		$("#modal_edicion_solicitud").modal();
		$("#modal_edicion_solicitud").attr('rel', codigo_solicitud);
		limpiar_campos("form_edicion_solicitud");
		llenar_formulario("form_edicion_solicitud", "tbl_deudores_creditos_solicitudes", "codigo_solicitud=" + codigo_solicitud);
		// Llenar selects de fuente
		rellenar_select("tbl_cuentas", "codigo", "nombre", "", "fuente_credito_edit", "");
	}

	function eliminar_solicitud(codigo_solicitud) {
		confirmar = confirm("¿Realmente deseas eliminar esta solicitud?");
		if (confirmar) {
			eliminar_solicitud_credito(codigo_solicitud);
		} else {
			return false;
		}
	}


	function eliminar_solicitud_credito(codigo_solicitud) {
		datos = "codigo_solicitud=" + codigo_solicitud;
		$.ajax({
			type: 'POST',
			async: true,
			url: 'ajax/eliminar_solicitud_credito.php',
			data: datos,
			success: function (data) {
				console.log(data);
				if (data == "1") {
					$.growl.notice({ title: "Exito!", message: "Solicitud eliminada correctamente" });
					cargar_listado_creditos();
				} else {
					$.growl.error({ title: "Error!", message: "No se pudo eliminar la solicitud" });
				}
			},
			dataType: 'json'
		});
	}

	function eliminar_credito(codigo_credito) {
		confirmar = confirm("¿Realmente deseas eliminar este credito?");
		if (confirmar) {
			eliminar_credito_respuesta(codigo_credito);
		} else {
			return false;
		}
	}

	function eliminar_credito_respuesta(codigo_credito) {
		datos = "codigo_credito=" + codigo_credito;
		$.ajax({
			type: 'POST',
			async: true,
			url: 'ajax/eliminar_credito.php',
			data: datos,
			success: function (data) {
				console.log(data);
				if (data == "1") {
					$.growl.notice({ title: "Exito!", message: "Credito eliminado correctamente" });
					cargar_listado_creditos();
				} else {
					$.growl.error({ title: "Error!", message: "No se pudo eliminar el credito" });
				}
			},
			dataType: 'json'
		});
	}


	function grabar_edicion_solicitud() {
		result = validar_formulario("1", "form_edicion_solicitud");
		if (result) {
			datos = $("#form_edicion_solicitud").serialize();
			$.ajax({
				type: 'POST',
				async: true,
				url: 'ajax/editar_solicitud_credito.php',
				data: datos,
				dataType: 'json',
				success: function (data) {
					console.log(data["mensaje"]);
					if (data["resultado"] == 1) {
						$("#modal_edicion_solicitud").modal('hide');
						$.growl.notice({ title: "Excelente", message: data["mensaje"] });
						cargar_listado_creditos();
					} else {
						$.growl.error({ title: "Error", message: data["mensaje"] });
					}
				}

			});
		}
	}

	function aprobar_solicitud(codigo_solicitud) {
		$("#modal_aprobacion_creditos").modal();
		$("#modal_aprobacion_creditos").attr('rel', codigo_solicitud);
		$("#codigo_solicitud_aprobacion").val(codigo_solicitud);
		mostrar_consulta("div_datos_solicitud", "datos_solicitud_credito", "codigo_solicitud=" + codigo_solicitud);
		document.form_aprobar_solicitud.reset();
	}


	function grabar_aprobacion() {
		result = validar_formulario("1", "form_aprobar_solicitud");
		if (result) {
			datos = $("#form_aprobar_solicitud").serialize();
			$.ajax({
				type: 'POST',
				async: true,
				url: 'ajax/aprobar_solicitud_credito.php',
				data: datos,
				dataType: 'json',
				success: function (data) {
					console.log(data);
					if (data["resultado"] == 1) {
						$.growl.notice({ title: "Excelente", message: data["mensaje"] });
						
						// Actualizar ambos listados: pendientes y aprobados
						cargar_listado_creditos();
						
						// Cerrar el modal de aprobación
						$("#modal_aprobacion_creditos").modal('hide');
						
						// Limpiar el formulario
						document.form_aprobar_solicitud.reset();
						
					} else {
						$.growl.error({ title: "Error", message: data["mensaje"] });
					}
				},
				error: function(xhr, status, error) {
					$.growl.error({ title: "Error", message: "Ocurrió un error al procesar la solicitud" });
					console.error("Error en la petición AJAX:", error);
				}
			})
		}
	}


	function pagar_cuota(codigo_cuota) {
		$("#modal_coutas_creditos").modal();
		$("#codigo_cuota_credito").val(codigo_cuota);
		$("#modal_listado_coutas_creditos").modal('hide');

		// Limpiar el label antes de la consulta
		$("#lbl_valor_incremento").text("Calculando...");

		// Consultar el valor incremento por AJAX
		$.ajax({
			type: 'POST',
			url: 'ajax/consultar_datos_cuota.php',
			data: { codigo_cuota: codigo_cuota },
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					if (data.valor_incremento > 0) {
						$("#lbl_valor_incremento").text("$" + formatNumber(data.valor_incremento) + " (" + data.dias_atraso + " días de atraso)");
					} else {
						$("#lbl_valor_incremento").text("Sin incremento por mora");
					}
				} else {
					$("#lbl_valor_incremento").text("No disponible");
				}
			},
			error: function () {
				$("#lbl_valor_incremento").text("Error al consultar");
			}
		});
	}

	function grabar_deudor() {
		campos = $("#reg_usuarios").serialize();

		$.ajax({
			type: 'POST',
			async: false,
			url: 'ajax/registrar_deudor.php',
			data: campos,
			success: function (data) {
				$.growl.notice({ title: "Resultado", message: data });
				$("#modal_reg_deudor").modal('hide');

			},
			dataType: 'text'
		});
	}

	function grabar_credito() {
		campos = $("#reg_creditos").serialize();

		$.ajax({
			type: 'POST',
			async: false,
			url: 'ajax/registrar_credito.php',
			data: campos,
			success: function (data) {
				alert(data);
				$("#modal_reg_credito").modal('hide');
				//document.reg_ahorros.reset();
				cargar_listado_creditos();
			},
			dataType: 'text'
		});
	}

	function grabar_cuota_credito() {
		campos = $("#reg_cuotas").serialize();

		$.ajax({
			type: 'POST',
			async: false,
			url: 'ajax/registrar_cuota_credito.php',
			data: campos,
			success: function (data) {
				$.growl.notice({ title: "Resultado", message: data });
				$("#modal_coutas_creditos").modal('hide');
				document.reg_cuotas.reset();
				$("#modal_listado_coutas_creditos").modal();
				cargar_listado_creditos();
				ver_cuotas($("#modal_coutas_creditos").attr('rel'));
			},
			dataType: 'text'
		});
	}

	function diferenciaDias(fecha_desde, fecha_hasta){
		let fecha1 = new Date(fecha_desde);
		let fecha2 = new Date(fecha_hasta);
		let diferencia = fecha2.getTime() - fecha1.getTime();
		let diasDeDiferencia = diferencia / 1000 / 60 / 60 / 24;
		return diasDeDiferencia; 
	}

	function cargar_total(){
		valor_solicitado=parseInt($("#rango_monto_valor").val());
		meses=parseInt($("#meses").html());
		tasa_interes=parseFloat($("#interes_sim").html());
		total_pagar=eval(((valor_solicitado*meses)*tasa_interes)/100);
		total_pagar=total_pagar+valor_solicitado;
		valor_mes=Math.round(eval(total_pagar/meses));
		valor_quincena=Math.round(eval(valor_mes/2));
		valor_diario = Math.round(eval(((valor_solicitado*tasa_interes)/100)/30));
		console.log(valor_diario+" - "+valor_quincena);

		$("#valor_dia").text(formatNumber(valor_diario));
		$("#valor_mensual").text(formatNumber(valor_mes));
		$("#valor_quincenal").text(formatNumber(valor_quincena));
		$("#valor_cuota").text(formatNumber(parseInt(total_pagar)));
		$("#lbl_mensual").text(meses+ " Cuota(s) Mensual(es) $");
		$("#lbl_quincenal").text(eval(meses*2)+ " Cuota(s) Quincenal(es) $");
		// calcular el valor total por los dias seleccionados
		diferencia_dias=diferenciaDias($("#fecha_desde").val(),$("#fecha_hasta").val());
		valor_total = formatNumber(parseInt(diferencia_dias*valor_diario)+valor_solicitado);
		//redondear el valor_total al entero mas cercano
		$("#valor_total_diario").text(formatNumber(valor_total));
		
	}

	// Proyección de cuotas en edición de solicitud
	function cargar_proyeccion_cuotas_edicion() {
		var valor_prestamo = $("#form_edicion_solicitud #valor_prestamo").val();
		var num_cuotas = $("#form_edicion_solicitud #num_cuotas").val();
		var tasa_sim = $("#form_edicion_solicitud #codigo_tasa_interes").val();
		var tiempo_cuotas = $("#form_edicion_solicitud #tiempo_cuota").val();
		var tipo_interes = $("#form_edicion_solicitud #tipo_interes_edit").val();
		var fecha_prestamo = new Date();
		var fecha_actual = fecha_prestamo.getFullYear() + '-' + String(fecha_prestamo.getMonth()+1).padStart(2, '0') + '-' + String(fecha_prestamo.getDate()).padStart(2, '0');

		if (!valor_prestamo || !num_cuotas || !tasa_sim || !tiempo_cuotas) {
			$('#tabla_proyeccion_cuotas_edicion').html('').hide();
			$("#form_edicion_solicitud #total_pagar").val("");
			return;
		}

		var tasa_decimal = tasa_sim / 100;
		
		if (tipo_interes == 1) {
			// Interés Fijo - cálculo actual
			total_pagar = parseInt(valor_prestamo) + ((parseInt(valor_prestamo) * tasa_decimal / tiempo_cuotas)* parseInt(num_cuotas));
		} else {
			// Interés Variable - calcular total sumando todas las cuotas
			var saldo_restante = parseInt(valor_prestamo);
			var amortizacion = saldo_restante / parseInt(num_cuotas);
			var total_interes = 0;
			
			for (var i = 1; i <= parseInt(num_cuotas); i++) {
				var interes_cuota = saldo_restante * tasa_decimal;
				total_interes += interes_cuota;
				saldo_restante -= amortizacion;
			}
			
			total_pagar = parseInt(valor_prestamo) + total_interes;
		}

		function formatearMoneda(valor) {
			let redondeado = Math.ceil(valor / 100) * 100;
			return redondeado.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });
		}
		$("#form_edicion_solicitud #total_pagar").val(formatearMoneda(total_pagar));

		$.ajax({
			url: 'ajax/proyeccion_cuotas.php',
			method: 'POST',
			data: {
				fecha_prestamo: fecha_actual,
				valor_prestamo: valor_prestamo,
				num_cuotas: num_cuotas,
				tiempo_cuota: tiempo_cuotas,
				tasa: tasa_sim,
				total_pagar: total_pagar,
				tipo_interes: $("#tipo_interes_edit").val()
			},
			dataType: 'json',
			success: function(respuesta) {
				var tabla = '<div class="table-responsive"><table class="table table-striped table-hover table-bordered" style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px #0001;">';
				tabla += '<thead class="thead-dark"><tr><th style="background:#47478D;color:white;">Fecha</th><th style="background:#47478D;color:white;">Valor</th></tr></thead><tbody>';
				respuesta.forEach(function(cuota) {
					tabla += '<tr><td>' + cuota.fecha + '</td><td>' + formatearMoneda(cuota.valor) + '</td></tr>';
				});
				tabla += '</tbody></table></div>';
				$('#tabla_proyeccion_cuotas_edicion').html(tabla).show();
			}
		});
	}

	// Disparar la proyección cuando cambie algún campo relevante en edición
	$('#form_edicion_solicitud #valor_prestamo, #form_edicion_solicitud #num_cuotas, #form_edicion_solicitud #codigo_tasa_interes, #form_edicion_solicitud #tiempo_cuota, #form_edicion_solicitud #tipo_interes_edit').on('change keyup', function() {
		cargar_proyeccion_cuotas_edicion();
	});

	// Incluir Chart.js si no está incluido
	if (typeof Chart === 'undefined') {
		var script = document.createElement('script');
		script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
		document.head.appendChild(script);
	}

	// JS para cargar el select de fuente y mostrar totales
	$(document).ready(function () {
		rellenar_select("tbl_cuentas", "codigo", "nombre", "", "filtro_fuente_grafico", "");
	});

	// JS para recargar el gráfico al cambiar filtros
	function cargarGraficoFuentes() {
		var estado = $('#filtro_estado_fuente').val();
		var fecha_desde = $('#filtro_fecha_desde_fuente').val();
		var fecha_hasta = $('#filtro_fecha_hasta_fuente').val();
		var fuente = $('#filtro_fuente_grafico').val();
		// Salidas (créditos)
		$.ajax({
			url: 'ajax/estadisticas_fuentes.php',
			method: 'GET',
			data: {
				estado: estado,
				fecha_desde: fecha_desde,
				fecha_hasta: fecha_hasta,
				fuente: fuente
			},
			dataType: 'json',
			success: function(data) {
				var ctx = document.getElementById('grafico_fuentes').getContext('2d');
				if (window.graficoFuentes) window.graficoFuentes.destroy();
				window.graficoFuentes = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: data.labels,
						datasets: [{
							label: 'Total por fuente',
							data: data.valores,
							backgroundColor: data.colores,
							borderColor: '#47478D',
							borderWidth: 1
						}]
					},
					options: {
						responsive: true,
						scales: {
							y: {
								beginAtZero: true,
								ticks: {
									callback: function(value) {
										return value.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });
									}
								}
							}
						}
					}
				});
				// Mostrar total
				var total = data.valores.reduce((a, b) => a + b, 0);
				$('#total_salidas_fuente').text('Total salidas: ' + total.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }));
			}
		});
		// Entradas (cuotas pagadas)
		$.ajax({
			url: 'ajax/estadisticas_fuentes_entradas.php',
			method: 'GET',
			data: {
				estado: estado,
				fecha_desde: fecha_desde,
				fecha_hasta: fecha_hasta,
				fuente: fuente
			},
			dataType: 'json',
			success: function(data) {
				var ctx = document.getElementById('grafico_fuentes_entradas').getContext('2d');
				if (window.graficoFuentesEntradas) window.graficoFuentesEntradas.destroy();
				window.graficoFuentesEntradas = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: data.labels,
						datasets: [{
							label: 'Total por fuente (entradas)',
							data: data.valores,
							backgroundColor: data.colores,
							borderColor: '#198754',
							borderWidth: 1
						}]
					},
					options: {
						responsive: true,
						scales: {
							y: {
								beginAtZero: true,
								ticks: {
									callback: function(value) {
										return value.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });
									}
								}
							}
						}
					}
				});
				// Mostrar total
				var total = data.valores.reduce((a, b) => a + b, 0);
				$('#total_entradas_fuente').text('Total entradas: ' + total.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }));
			}
		});
	}

	$(document).ready(function () {
		$('#fuentes-tab').on('shown.bs.tab', function () {
			cargarGraficoFuentes();
		});
		$('#btn_mostrar_graficos_fuente').on('click', function(e) {
			e.preventDefault();
			cargarGraficoFuentes();
		});
		$('#filtro_estado_fuente, #filtro_fecha_desde_fuente, #filtro_fecha_hasta_fuente').on('change', function() {
			cargarGraficoFuentes();
		});
	});

</script>

<div class="page-header" style="background: linear-gradient(135deg, #47478D 0%, #5a5a9e 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(71, 71, 141, 0.2);">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2 style="margin: 0; font-weight: 600;"><i class="fi-rr-credit-card" style="margin-right: 15px;"></i>Gestión de Créditos</h2>
			<p style="margin: 10px 0 0 0; opacity: 0.9;">Administra solicitudes, créditos aprobados y simulaciones</p>
		</div>
		<div class="col-md-4 text-right">
			<div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;">
				<div style="font-size: 12px; opacity: 0.8;">Sistema de Créditos</div>
				<div style="font-size: 18px; font-weight: 600;">SAF</div>
			</div>
		</div>
	</div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="aprobados-tab" data-toggle="tab" href="#aprobados" role="tab"
			aria-controls="aprobados" aria-selected="true">Aprobados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="pendientes-tab" data-toggle="tab" href="#pendientes" role="tab"
			aria-controls="pendientes" aria-selected="false">Pendientes</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="simular-tab" data-toggle="tab" href="#simular" role="tab" aria-controls="simular"
			aria-selected="false">Simular Crédito</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="fuentes-tab" data-toggle="tab" href="#fuentes" role="tab" aria-controls="fuentes"
			aria-selected="false">Estadística por Fuente</a>
	</li>
</ul>
<br>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="aprobados" role="tabpanel" aria-labelledby="aprobados-tab">
		<div class="action-buttons" style="margin-bottom: 30px;">
			<div class="row">
				<div class="col-md-6">
					<button type="button" class="btn btn-primary btn-lg btn-block" id="btn_crear_deudor" data-toggle="modal" data-target="#modal_reg_deudor" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
						<i class="fi-rr-user" style="margin-right: 10px;"></i>Agregar Deudor
					</button>
				</div>
				<div class="col-md-6">
					<button type="button" class="btn btn-primary btn-lg btn-block" id="btn_crear_credito" data-toggle="modal" data-target="#modal_reg_credito" style="background: linear-gradient(135deg, #47478D 0%, #5a5a9e 100%); border: none; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(71, 71, 141, 0.3);">
						<i class="fi-rr-file-add" style="margin-right: 10px;"></i>Agregar Crédito
					</button>
				</div>
			</div>
		</div>
		<div class="totales-container">
			<div class="total-item">
				<div class="total-label">Total Pagado</div>
				<div class="total-valor" id="spn_pagado">$0</div>
			</div>
			<div class="total-item">
				<div class="total-label">Total Pendiente</div>
				<div class="total-valor" id="spn_pendiente">$0</div>
			</div>
		</div>

		<hr><br>


		<ul class="nav nav-tabs">
			<li class="nav-item">
				<span class="nav-link active" href="">Datos</span>
			</li>
		</ul>

		<br>

		<div class="filtros-container">
			<div class="row">
				<div class="col-md-4">
					<div class="input-group mb-3 admin" style="display:none">
						<div class="input-group-prepend">
							<span class="input-group-text">Asesor</span>
						</div>
						<select class="form-control" id="codigo_asesor_filtro"></select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">Estado</span>
						</div>
						<select id="codigo_estado_filtro" class="form-control"></select>
					</div>
				</div>
				<div class="col-md-4">
					<button type="button" class="btn btn-primary btn-block" id="btn_consultar">
						<i class="fi-rr-search"></i> Consultar
					</button>
				</div>
			</div>
		</div>
		<br>

		<div id="div_listado_creditos" class="div_texto_oscuro"></div>
	</div>
	<div class="tab-pane fade" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
		<div id="div_listado_creditos_pendientes" class="div_texto_oscuro"></div>
	</div>
	<div class="tab-pane fade" id="simular" role="tabpanel" aria-labelledby="simular-tab">
		<div id="div_simulador">
			<div id="div_detalle_simulador">
				<p>
					<center>
						<div class="slidecontainer">

							<p>Tiempo en Meses: <span id="meses"></span></p>
							<input type="range" min="1" max="24" value="1" class="slider" id="rango_meses">

						</div>
					</center>
				</p>
				<p>
					<center>
						<div class="slidecontainer">

							<p>Monto solicitado: <span id="monto"></span></p>
							<input type="hidden" value="">
							<input type="range" min="100000" max="3000000" value="10000" step="50000" class="slider"
								id="rango_monto">
							<input type="hidden" id="rango_monto_valor" value="">

						</div>
					</center>
				</p>

				<p>
					<center>
						<div class="slidecontainer">

							<p> Tasa interés: <span id="interes_sim"></span></p>
							<input type="range" min="5" max="20" value="5" step="0.5" class="slider" id="rango_interes">

						</div>
					</center>
				</p>
				<p>
					<center>
						<div class="container">
							<div class="row">
								<div class="col-md">
									<div class="input-group-prepend">
										<span class="input-group-text" id="">Fecha Desde</span>
									</div>
									<input type="date" autocomplete="off" id="fecha_desde" name="fecha_desde"
										class="form-control" placeholder="" aria-label="Username" aria-describedby=""
										onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
								</div>
								<div class="col-md">
									<div class="input-group-prepend">
										<span class="input-group-text" id="">Fecha Hasta</span>
									</div>
									<input type="date" autocomplete="off" id="fecha_hasta" name="fecha_hasta"
										class="form-control" placeholder="" aria-label="Username" aria-describedby=""
										onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
								</div>
							</div>
						</div>
					</center>
				</p>
				<p>
					<center>
						<div style="margin-top: 10%;">
							<p>
								<label>Valor Diaria: $</label>
								<span id="valor_dia" style="width: 100px;"></span>
							</p>
							<p>
								<label id="lbl_valor_total_diario">Valor total con intereses diarios: $</label>
								<span id="valor_total_diario" style="width: 100px;"></span>
							<p>
								<label id="lbl_mensual">Cuota Mensual: $</label>
								<span id="valor_mensual" style="width: 100px;"></span>
							</p>
							<p>
								<label id="lbl_quincenal">Cuota Quincenal: $</label>
								<span id="valor_quincenal" style="width: 100px;"></span>
							</p>
							<p>
								<label id="lbl_total">Valor a Pagar: $</label>
								<span id="valor_cuota" style="width: 100px;"></span>
							</p>
						</div>
					</center>
				</p>
			</div>
		</div>

	</div>
	<div class="tab-pane fade" id="fuentes" role="tabpanel" aria-labelledby="fuentes-tab">
		<h4>Balance de valores por fuente</h4>
		<div class="row mb-3">
			<div class="col-md-3">
				<label>Estado:</label>
				<select id="filtro_estado_fuente" class="form-control">
					<option value="">Todos</option>
					<option value="1">Activos</option>
					<option value="2">Pagados</option>
					<option value="3">Pendientes</option>
				</select>
			</div>
			<div class="col-md-3">
				<label>Fecha desde:</label>
				<input type="date" id="filtro_fecha_desde_fuente" class="form-control" />
			</div>
			<div class="col-md-3">
				<label>Fecha hasta:</label>
				<input type="date" id="filtro_fecha_hasta_fuente" class="form-control" />
			</div>
			<div class="col-md-3">
				<label>Fuente:</label>
				<select id="filtro_fuente_grafico" class="form-control">
					<option value="">Todas</option>
				</select>
			</div>
		</div>
		<div class="mb-3">
			<button class="btn btn-primary" id="btn_mostrar_graficos_fuente">Mostrar Gráficos</button>
		</div>
		<div class="row">
			<div class="col-md-6">
				<h5>Salidas por fuente (Créditos otorgados)</h5>
				<canvas id="grafico_fuentes" height="120"></canvas>
				<div id="total_salidas_fuente" class="font-weight-bold mb-4"></div>
			</div>
			<div class="col-md-6">
				<h5>Entradas por fuente (Cuotas pagadas)</h5>
				<canvas id="grafico_fuentes_entradas" height="120"></canvas>
				<div id="total_entradas_fuente" class="font-weight-bold"></div>
			</div>
		</div>
	</div>
</div>