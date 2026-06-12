<!-- Modal -->
<div class="modal fade" id="modal_reg_deudor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Crear Deudor</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<form class="reg_usuarios" name="reg_usuarios" id="reg_usuarios">	

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Asesor</span>
						</div>
						<select class="form-control" name="codigo_asesor" id="codigo_asesor" lang="1"></select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">N° Documento</span>
						</div>
						<input type="text" autocomplete="off" name="num_documento" lang="1" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Nombres</span>
						</div>
						<input type="text" autocomplete="off" name="nombres" lang="1" class="form-control" placeholder="" aria-label="Username" aria-describedby="">
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Apellidos</span>
						</div>
						<input type="text" autocomplete="off" name="apellidos" lang="1" class="form-control" placeholder="" aria-label="Username" aria-describedby="">
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Dirección</span>
						</div>
						<input type="text" autocomplete="off" name="direccion" lang="1" class="form-control" placeholder="" aria-label="Username" aria-describedby="">
					</div>


					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Telefono</span>
						</div>
						<input type="text" autocomplete="off" name="telefono" lang="1" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
					</div>

				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" id="btn_grabar_deudor">Grabar</button>
			</div>
		</div>
	</div>
</div>


<!-- Modal ahorro-->
<div class="modal fade" id="modal_reg_credito" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Crear Crédito</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<form class="reg_creditos" name="reg_creditos" id="reg_creditos">	

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Deudor:</span>
						</div>
						<select class="form-control" name="codigo_deudor" id="codigo_deudor"></select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Fecha de Préstamo</span>
						</div>

						<input type="date" autocomplete="off" name="fecha_prestamo" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
					</div>


					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Valor Préstamo</span>
						</div>

						<input type="text" autocomplete="off" name="valor_prestamo" id="valor_prestamo" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onkeydown="noPuntoComa(event)" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Tiempo Cuotas</span>
						</div>
						<select class="form-control" name="tiempo_cuota" id="tiempo_cuota">
							<option value="0">Seleccione</option>
							<option value="1">Mensual</option>
							<option value="2">Quincenal</option>
							<option value="4">Semanal</option>
						</select>		
					</div>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Número de Cuotas</span>
						</div>

						<input type="text" autocomplete="off" name="num_cuotas" id="num_cuotas" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onkeydown="noPuntoComa(event)" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Tipo de Interés</span>
						</div>
						<select class="form-control" name="tipo_interes" id="tipo_interes">
							<option value="1">Interés Fijo</option>
							<option value="2">Interés Variable</option>
						</select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Tasa interés</span>
						</div>
						<select class="form-control" name="codigo_tasa_interes_reg" id="codigo_tasa_interes_reg" class="codigo_tasa_interes"></select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Fuente</span>
						</div>
						<select class="form-control" name="fuente_credito_reg" id="fuente_credito_reg"></select>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Total a Pagar</span>
						</div>

						<input type="text" autocomplete="off" id="total_pagar" name="total_pagar" class="form-control" placeholder="" aria-label="Username" aria-describedby="" >		
					</div>
					<!-- Proyección de cuotas -->
					<div id="tabla_proyeccion_cuotas"></div>

				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" id="btn_grabar_credito">Grabar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal registro cuotas-->
<div class="modal fade" id="modal_coutas_creditos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_couta">Registro de cuota de Crédito</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<form class="reg_cuotas" name="reg_cuotas" id="reg_cuotas">	

					<input type="hidden" id="codigo_cuota_credito" name="codigo_cuota">

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Interés</span>
						</div>
						<select class="form-control" name="interes" id="interes">
							<option value="1">No</option>
							<option value="2">Si</option>
						</select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Valor pagado</span>
						</div>

						<input type="text" autocomplete="off" id="valor_pagado" name="valor_pagado" class="form-control" placeholder="" aria-label="" aria-describedby="" >		
					</div>
					<!-- Label para mostrar el valor incremento por mora -->
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Incremento por mora</span>
						</div>
						<label class="form-control" id="lbl_valor_incremento" style="background:#f8d7da;color:#721c24;">$0</label>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Fuente</span>
						</div>
						<select class="form-control" name="fuente_cuota" id="fuente_cuota"></select>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Fecha de Pago</span>
						</div>

						<input type="date" autocomplete="off" name="fecha_registro_pago" id="fecha_registro_pago" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Comentarios</span>
						</div>

						<textarea class="form-control" name="comentarios" id="comentarios" rows="3"></textarea>		
					</div>

				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" id="btn_grabar_cuota">Grabar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal registro cuotas-->
<div class="modal fade" id="modal_listado_coutas_creditos" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_couta">Listado de cuotas</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="div_listado_cuotas_creditos"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal edición solicitudes-->
<div class="modal fade" id="modal_edicion_solicitud" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_couta">Edición de Solicitud</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<form class="form_edicion_solicitud" name="form_edicion_solicitud" id="form_edicion_solicitud">	

					<input type="hidden" id="codigo_solicitud" name="codigo_solicitud">

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Asesor</span>
						</div>
						<select class="form-control" name="codigo_asesor" id="codigo_asesor" lang="1"></select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">N° Documento</span>
						</div>
						<input type="text" autocomplete="off" name="documento" lang="1" class="form-control" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Nombres</span>
						</div>
						<input type="text" autocomplete="off" name="nombres" lang="1" class="form-control" aria-describedby="">
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Apellidos</span>
						</div>
						<input type="text" autocomplete="off" name="apellidos" lang="1" class="form-control" aria-describedby="">
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Dirección</span>
						</div>
						<input type="text" autocomplete="off" name="direccion" lang="1" class="form-control" aria-describedby="">
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Email</span>
						</div>
						<input type="text" autocomplete="off" name="email" lang="1" class="form-control" aria-describedby="">
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Telefono</span>
						</div>
						<input type="text" autocomplete="off" name="telefono" lang="1" class="form-control" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Fuente</span>
						</div>
						<select class="form-control" name="fuente_credito_edit" id="fuente_credito_edit"></select>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Valor Solicitado</span>
						</div>

						<input type="text" autocomplete="off" id="valor_prestamo" lang="1" name="valor_prestamo" class="form-control" aria-describedby="" >		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Tiempo Cuotas</span>
						</div>
						<select class="form-control" name="tiempo_cuota" id="tiempo_cuota" lang="1">
							<option value="0">Seleccione</option>
							<option value="1">Mensual</option>
							<option value="2">Quincenal</option>
						</select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Número de Cuotas</span>
						</div>

						<input type="text" autocomplete="off" name="num_cuotas"  id="num_cuotas" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onkeydown="noPuntoComa(event)" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Tipo de Interés</span>
						</div>
						<select class="form-control" name="tipo_interes" lang="1" id="tipo_interes_edit">
							<option value="1">Interés Fijo</option>
							<option value="2">Interés Variable</option>
						</select>		
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Tasa interés</span>
						</div>
						<select class="form-control" name="codigo_tasa_interes" lang="1" id="codigo_tasa_interes"></select>		
					</div>
					<!-- Proyección de cuotas en edición -->
					<div id="tabla_proyeccion_cuotas_edicion"></div>
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" id="btn_editar_solicitud">Grabar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Aprobación de Créditos-->
<div class="modal fade" id="modal_aprobacion_creditos" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_couta">Aprobación de Crédito</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<form class="form_aprobar_solicitud" name="form_aprobar_solicitud" id="form_aprobar_solicitud">	
				<input type="hidden" id="codigo_solicitud_aprobacion" name="codigo_solicitud">
				<div id="div_datos_solicitud"></div>
				<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="">Estado</span>
						</div>
						<select class="form-control" name="codigo_estado" id="codigo_estado" lang="1">
							<option value="0">Seleccione</option>
							<option value="1">Aprobada</option>
							<option value="2">No aprobada</option>
						</select>		
				</div>
				<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" >Observación</span>
						</div>
						<textarea class="form-control" id="observacion" lang="1" name="observacion" rows="3"></textarea>
				</div>
			</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn_aprobar_solicitud">Grabar</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

