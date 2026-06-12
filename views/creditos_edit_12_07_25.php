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
</style>
<script src="js/jquery.growl.js" type="text/javascript"></script>
<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />
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
		rellenar_select("tbl_tasa_interes", "codigo_tasa_interes", "concat(valor,'%')", "", "form_edicion_solicitud select#codigo_tasa_interes", "", "codigo_tasa_interes");
		

		$('#codigo_asesor_filtro').bind('change', function () { cargar_listado_creditos(); });
		$('#codigo_estado_filtro').bind('change', function () { cargar_listado_creditos(); });

		$('#codigo_tasa_interes_reg').bind('change', function () {
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
		total_pagar = Math.round(eval(((valor_prestamo * num_cuotas) / tiempo_cuotas) * tasa_sim) / 100);
		total_pagar += parseInt(valor_prestamo);
		$("#total_pagar").val(total_pagar);
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
						cargar_listado_creditos();
					} else {
						$.growl.error({ title: "Error", message: data["mensaje"] });
					}
					$("#modal_aprobacion_creditos").modal('hide');
				}

			})
		}
	}


	function pagar_cuota(codigo_cuota) {
		$("#modal_coutas_creditos").modal();
		$("#codigo_cuota_credito").val(codigo_cuota);
		$("#modal_listado_coutas_creditos").modal('hide');


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

</script>

<div style="display: flex;">
	<div style="width: 50%">
		<h3>Créditos</h3>
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
</ul>
<br>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="aprobados" role="tabpanel" aria-labelledby="aprobados-tab">
		<p>
			<button type="button" class="btn btn-primary" id="btn_crear_deudor" data-toggle="modal"
				data-target="#modal_reg_deudor" style="    margin: 3px;  ">
				Agregar Deudor <i class="fi-rr-user"></i>
			</button>
			<button type="button" class="btn btn-primary" id="btn_crear_credito" data-toggle="modal"
				data-target="#modal_reg_credito" style="    margin: 3px;    ">
				Agregar Crédito <i class="fi-rr-file-add"></i>
			</button>
		</p>
		<p>
		<div id="div_valores_pagos" class="input-group mb-3">
			<div class="input-group-prepend">
				<span class="input-group-text">TOTAL PAGADO:</span>
				<span id="spn_pagado" class="input-group-text"></span>
			</div>
		</div>
		<div id="div_valores_pagos" class="input-group mb-3">
			<div class="input-group-prepend">
				<span class="input-group-text">TOTAL PENDIENTE:</span>
				<span id="spn_pendiente" class="input-group-text"></span>
			</div>
		</div>
		</p>

		<hr><br>


		<ul class="nav nav-tabs">
			<li class="nav-item">
				<span class="nav-link active" href="">Datos</span>
			</li>
		</ul>

		<br>

		<div>
			<div class="input-group mb-3 admin" style="display:none">
				<div class="input-group-prepend">
					<span class="input-group-text">Asesor</span>
				</div>
				<select class="form-filtro" id="codigo_asesor_filtro"></select>
			</div>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text">Estado:</span>
				</div>
				<select id="codigo_estado_filtro" class="form-filtro"></select>
			</div>

			<p>
				<button type="button" class="btn btn-primary" id="btn_consultar" style="margin: 3px;">
					Consultar <i class="fi-rr-search"></i>
				</button>
			</p>
		</div>
		<br>

		<div id="div_listado_creditos"></div>
	</div>
	<div class="tab-pane fade" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
		<div id="div_listado_creditos_pendientes"></div>
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
</div>