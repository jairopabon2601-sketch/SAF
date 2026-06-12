<!-- Modal -->
<div class="modal fade" id="modal_reg_ahorrador" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear Ahorrador</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form class="reg_usuarios" name="reg_usuarios" id="reg_usuarios">	

        	<div class="input-group mb-3">
			 	<label>Asesor</label>
			  	<select class="form-control" name="codigo_asesor" id="codigo_asesor"></select>		
			</div>

	        <div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">N° Documento</span>
			  </div>
			  <input type="text" name="num_documento" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
			</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Nombres</span>
			  </div>
			  <input type="text" name="nombres" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1">
			</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Apellidos</span>
			  </div>
			  <input type="text" name="apellidos" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1">
			</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Dirección</span>
			  </div>
			  <input type="text" name="direccion" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1">
			</div>


			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Telefono</span>
			  </div>
			  <input type="text" name="telefono" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
			</div>
			
		</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn_grabar_ahorrador">Grabar</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal ahorro-->
<div class="modal fade" id="modal_reg_ahorro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear Ahorro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>



      <div class="modal-body">

        <form class="reg_ahorros" name="reg_ahorros" id="reg_ahorros">	

        <div class="input-group mb-3">
			  	<label>Ahorrador:</label>
			  	<select class="form-control" name="codigo_ahorrador" id="codigo_ahorrador"></select>		
				</div>

				<div class="input-group mb-3">
			  	<div class="input-group-prepend">
			    	<span class="input-group-text" id="basic-addon1">Año de Ahorro:</span>
			  	</div>

			  	  	<select class="form-control" name="codigo_anio_ahorro" id="codigo_anio_ahorro"></select>		
				</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Fecha de Ingreso</span>
			  </div>

			  <input type="date" name="fecha_ingreso" id="fecha_ingreso_ahorro" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
			</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Valor Pactado</span>
			  </div>

			  <input type="text" name="valor_pactado" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" onkeydown="noPuntoComa(event)" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
			</div>
		</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn_grabar_ahorro">Grabar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal CONF-->
<div class="modal fade" id="modal_confi_ahorro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Configurar Ahorro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form class="reg_conf_cuotas" name="reg_conf_cuotas" id="reg_conf_cuotas">	

        		<div class="input-group mb-3">
					<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1">Año</span>
					</div>
					<select name="anio" class="form-control">
						<option value="0">[Seleccione una Opción]</option>
						<option value="2021">2021</option>
						<option value="2022">2022</option>
						<option value="2023">2023</option>
						<option value="2024">2024</option>
					</select>
				</div>


				<div class="input-group mb-3">
				  <div class="input-group-prepend">
				    <span class="input-group-text" id="basic-addon1">Fecha Inicio</span>
				  </div>

				  <input type="date" name="fecha_inicio" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
				</div>

					<div class="input-group mb-3">
				  	<div class="input-group-prepend">
				    		<span class="input-group-text" id="basic-addon1">Fecha Final</span>
				  	</div>

				  	<input type="date" name="fecha_final" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
					</div>


					<div class="input-group mb-3">
				  		<div class="input-group-prepend">
				    		<span class="input-group-text" id="basic-addon1">Tiempo en Mes</span>
				  		</div>

				  		<input type="text" name="tiempo_mes" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="basic-addon1">Tipo</span>
						</div>
					<select name="tipo" class="form-control">
						<option value="0">[Seleccione una Opción]</option>
						<option value="1">Fijo</option>
						<option value="2">Variable</option>
					</select>
				</div>
        	
				</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn_grabar_conf_ahorro">Grabar</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal registro cuotas-->
<div class="modal fade" id="modal_coutas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_couta_nombre">Registro de cuota</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<form class="reg_cuotas" name="reg_cuotas" id="reg_cuotas">	

					<input type="hidden" id="codigo_cuota" name="codigo_cuota">

					<div id="div_registro_pago">

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="">Fecha de Pago</span>
							</div>

							<input type="date" name="fecha_registro_pago" id="fecha_registro_pago" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
						</div>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="">Valor Pagado</span>
							</div>

							<input type="text" name="valor_pagado" id="valor_pagado" class="form-control" placeholder="" aria-label="Username" aria-describedby="" onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;'>		
						</div>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="">Detalle del Ahorro</span>
							</div>
    						<textarea class="form-control" id="detalle" name="detalle" rows="3"></textarea>
						</div>

					</div>
					<div id="div_datos_cuota"></div>	


				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" id="btn_grabar_cuota">Grabar</button>
			</div>
		</div>
	</div>
</div>

