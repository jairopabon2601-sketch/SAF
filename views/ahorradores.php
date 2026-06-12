
<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT']. '/views/includes/form_windows_ahorradores.php');
?>


<style type="text/css">


	::-webkit-scrollbar {
	    width: 15px;
	}

	::-webkit-scrollbar-track {
	    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
	    border-radius: 10px;
	}
	
	::-webkit-scrollbar-thumb {
	    border-radius: 10px;
	    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
	}
	

</style>
<script type="text/javascript">

<?php
    echo "var codigo_perfil='" . $_SESSION["codigo_perfil"] . "';\n";
	echo "var codigo_asesor='" . $_SESSION["codigo_origen"] . "';\n";
    ?>

	f=new Date();
  anio_actual=f.getFullYear();

	$(document).ready(function() {
		$("#btn_crear_ahorrador").bind("click",agregar_ahorrador);
		$("#btn_crear_ahorro").bind("click",datos_ahorradores);

		$("#btn_grabar_ahorrador").bind("click",grabar_ahorrador);
		$("#btn_grabar_ahorro").bind("click",grabar_ahorro);
		$("#btn_grabar_cuota").bind("click",grabar_pago_cuota);
		$("#btn_grabar_conf_ahorro").bind("click",grabar_conf_ahorro);
		

		cargar_anio("anio_ahorro",2019,2026);

		$("#anio_ahorro").bind("change",cargar_ahorros);

		$('#tabla_ahorros').on('click', '.couta_mes', function(){
			index=$(".couta_mes").index(this);
			codigo_cuota=$(".couta_mes").eq(index).attr('codigo_cuota');
			nombre_mes=$(".couta_mes").eq(index).attr('nombre_mes');
			ahorrador=$(".couta_mes").eq(index).attr('ahorrador');
			estado_cuota = consultar_campo("tbl_ahorradores_cuotas","estado","codigo_cuota='"+codigo_cuota+"'");
			document.reg_cuotas.reset();
			$('#modal_coutas').modal();
			$("[name='codigo_cuota']").val(codigo_cuota);
			$("#title_couta_nombre").html("Ahorrador: "+ahorrador + "<br>Mes: "+nombre_mes);
			if (estado_cuota==1) {
				$("#div_registro_pago").hide();
				$("#btn_grabar_cuota").hide();
				mostrar_consulta("div_datos_cuota","detalle_pago_cuota","cuo.codigo_cuota="+codigo_cuota);
			}else{
				$("#div_registro_pago").show();
				$("#btn_grabar_cuota").show();
			}
				
		});

		$("#codigo_anio_ahorro").bind("click",function(){
			
			datos=consultar_campo("tbl_ahorro_anyos","fecha_inicio, fecha_fin","codigo_ahorro_anyo='"+$(this).val() +"'");
			data_fecha = datos.split(";");

			$("#fecha_ingreso_ahorro").attr("min",data_fecha[0]).attr("max",data_fecha[1]);
		
		});

		const element = document.querySelector("#container_ahorros");
		console.log(element);
		element.addEventListener('wheel', (event) => {
			event.preventDefault();

			element.scrollBy({
				left: event.deltaY < 0 ? -30 : 30,
				
			});
		});

		if (codigo_perfil!=6) {
			$("#anio_ahorro").prop("disabled","disabled");
		}

		cargar_ahorros();

	});


	function cargar_ahorros(){

		if ($("#anio_ahorro").val()==0) {
			$("#anio_ahorro").val(anio_actual);
		}

			$("#container_ahorros").show();

			$.ajax({
	          type: 'POST',
	          async: false,
	          url: 'ajax/listado_ahorros.php',
	          data: {
	          	anio_ahorro: $("#anio_ahorro").val()
	          },
	          success: function(data){

			   			if(data.resultado==0){
			   				
			   				$("#tabla_ahorros tbody").html(data.mensaje);
			   				$(".total_mes").html("");
			   				$("#tabla_ahorros thead tr").eq(1).html("");

			   			}else{
							tipo_ahorro=consultar_campo("tbl_ahorro_anyos","tipo","anyos='"+ $("#anio_ahorro").val() +"'");
			   				console.log(data);

								tabla="";
			   				x=0;
			   				total_ahorrado=0;
			   				total_rendimiento=0;
			   				total_pagar=0;
			   				$(data.ahorradores).each(function(index, el) {
			   					tabla+="<tr>";
			   						rendimiento=( parseFloat($(this)[0].neto_pagar) -  parseFloat($(this)[0].total_ahorrado) );

						      	tabla+="<td class='static'><b>"+$(this)[0].ahorrador+"</b></td>";
						      	tabla+="<td class='static'>"+$(this)[0].asesor+"</td>";
						      	tabla+="<td>"+$(this)[0].Fecha_ingreso+"</td>";	
								  tabla+="<td> $ "+formatNumber($(this)[0].total_ahorrado)+"</td>";
								if (tipo_ahorro==1) {
									tabla+="<td> $ "+formatNumber($(this)[0].Valor_pactado)+"</td>";								
									tabla+="<td>" + formatNumber(rendimiento) +  "</td>";
									tabla+="<td>$ "+formatNumber($(this)[0].neto_pagar)+"</td>";
								}

						      	total_ahorrado=total_ahorrado + parseFloat($(this)[0].total_ahorrado);
						      	total_rendimiento=total_rendimiento+rendimiento;
						      	total_pagar=total_pagar+parseFloat($(this)[0].neto_pagar);

						      	orden_cuota=$(this)[0].orden_cuota;
						      	nombre_ahorrador=$(this)[0].ahorrador;

							
										j=1;
										eq_ahorro=0;
							      
							      $(data.meses_ahorro).each(function(index, el) {
							      		nombre_mes_cuota=$(this)[0].nombre_mes;

							      		if (j>=orden_cuota) {
								      			tabla+="<td class='meses' ";

								      			codigo_estado_pago=data.ahorradores[x].ahorros[eq_ahorro].estado;
								     			estado_pago=data.ahorradores[x].ahorros[eq_ahorro].estado_pago;
								     			codigo_cuota=data.ahorradores[x].ahorros[eq_ahorro].codigo_cuota;
								     			nombre_mes=data.ahorradores[x].ahorros[eq_ahorro].nombre_mes;
								     			fecha_pago=data.ahorradores[x].ahorros[eq_ahorro].fecha_pago;
								     			valor_pagado=data.ahorradores[x].ahorros[eq_ahorro].valor_pagado;
								     			sigla_asesor=data.ahorradores[x].ahorros[eq_ahorro].sigla;
								     			mora=data.ahorradores[x].ahorros[eq_ahorro].mora;
								     			color=data.ahorradores[x].ahorros[eq_ahorro].color;

								     			if (mora==1) {
								     				tabla+="style='background:"+color+";color: black;font-weight: bold;' >";
								     			}else{
								     				tabla+=">";
								     			}

								     			tabla+="<a ";

								     			//CUOTAS NO PAGAS
								     			//if (codigo_estado_pago!=1) {
								     				tabla+=" class='couta_mes' "
								     			//}

								     			tabla+=" ahorrador='"+nombre_ahorrador+"' codigo_cuota='"+codigo_cuota+"' nombre_mes='"+nombre_mes_cuota+"'>"+estado_pago+"<br>";
													
								     				if (codigo_estado_pago==1) {
								     					tabla+=fecha_pago+"<br>$";
								     					tabla+=formatNumber(valor_pagado);

								     				}

								     			tabla+="</a>";
								     		

								     		tabla+="</td>";

								     		eq_ahorro++;

							      		}else{
							      			tabla+="<td></td>";
							      		}
							      		
							      		j++;
			   						});

							    	tabla+="</tr>";

							    x++;
			   				});


			   				$("#tabla_ahorros tbody").html(tabla);

			   				total_ahorrado=formatNumber(total_ahorrado);
			   				total_rendimiento=formatNumber(total_rendimiento);
			   				total_pagar=formatNumber(total_pagar);

							html='<th scope="col" class="static">Ahorrador</th>';
							html+='<th scope="col" class="static">Asesor</th>';
							html+='<th scope="col" >Fecha Ingreso</th>';
							html+='<th scope="col">Total Ahorrado</th>';
							if (tipo_ahorro==1) {
							html+='<th scope="col">Valor Pactado</th>';
							html+='<th scope="col">Rendimiento</th>';
							html+='<th scope="col">Neto a Pagar</th>';
							}

							html_valores='<td rowspan="1" colspan="3"></td>';
							html_valores+='<th scope="col" class="meses total_mes" id="total_ahorrado">$ ' +total_ahorrado+'</th>';
							if (tipo_ahorro==1) {
							html_valores+='<th scope="col" class="meses total_mes" id="total_rendimiento">$ '+total_rendimiento+'</th>';
							html_valores+='<th scope="col" class="meses total_mes" id="total_pagar">$ '+total_pagar+'</th>';
							}	

			   				$(data.meses_ahorro).each(function(index, el) {
			   					html+="<th scope='col' class='meses'>"+$(this)[0].nombre_mes+"</th>";
			   					html_valores+="<th scope='col' class='meses total_mes' id='total_1'>$ "+formatNumber($(this)[0].total_mes)+"</th>";	
			   				});

			   				$("#tabla_ahorros thead tr").eq(0).html(html_valores);
			   				$("#tabla_ahorros thead tr").eq(1).html(html);

			   				$("#container_ahorros").css("overflow","overlay");
			   			}
	        	},
	        dataType: 'json'
	     	});		
	}



	function agregar_ahorrador(){
		rellenar_select("tbl_asesores","codigo_asesor","concat(nombres,' ',apellidos)","","codigo_asesor");
		$("#codigo_asesor").chosen({
			width: "100%"
		});
	}

	function datos_ahorradores(){
		

		rellenar_select("tbl_ahorradores","codigo","concat(nombres,' ',apellidos)","","codigo_ahorrador","","nombres");

		rellenar_select("tbl_ahorro_anyos","codigo_ahorro_anyo","concat(anyos,' (',fecha_inicio,' hasta ',fecha_fin,')')","","codigo_anio_ahorro","","anyos");
		$("#codigo_ahorrador").chosen({
			width: "100%"
		});

		$("#codigo_ahorrador").val('').trigger("chosen:updated");
	}

	function grabar_ahorrador(){
		campos=$("#reg_usuarios").serialize();

		$.ajax({
          type: 'POST',
          async: false,
          url: 'ajax/registrar_ahorradores.php',
          data: campos,
          success: function(data){
            alert(data);
            $("#modal_reg_ahorrador").modal('hide');
            document.reg_usuarios.reset();
            $("#codigo_asesor").val('').trigger("chosen:updated");
            cargar_ahorros();
          },
          dataType: 'text'
     	});
	}

	function grabar_ahorro(){
		campos=$("#reg_ahorros").serialize();

		$.ajax({
          type: 'POST',
          async: false,
          url: 'ajax/registrar_ahorro.php',
          data: campos,
          success: function(data){
            alert(data);
            $("#modal_reg_ahorro").modal('hide');
            document.reg_ahorros.reset();
            $("#codigo_ahorrador").val('').trigger("chosen:updated");
            cargar_ahorros();
          },
          dataType: 'text'
     	});
	}

	function grabar_pago_cuota(){
		campos=$("#reg_cuotas").serialize();

		$.ajax({
          type: 'POST',
          async: false,
          url: 'ajax/registrar_pago_cuota.php',
          data: campos,
          success: function(data){
            alert(data);
            $("#modal_coutas").modal('hide');
            document.reg_cuotas.reset();
            cargar_ahorros();
          },
          dataType: 'text'
     	});
	}

	function grabar_conf_ahorro(){
		campos=$("#reg_conf_cuotas").serialize();
		$.ajax({
          type: 'POST',
          async: false,
          url: 'ajax/registrar_conf_cuota.php',
          data: campos,
          success: function(data){
            alert(data);
            $("#modal_confi_ahorro").modal('hide');
            document.reg_conf_cuotas.reset();
            cargar_ahorros();
          },
          dataType: 'text'
     	});
	}

</script>

<div style="display: flex;">
	<div style="width: 50%"><h3>Ahorradores</h3></div>
</div>

<?php
if($_SESSION['codigo_perfil']==6){
?>
<p id="p_funciones">
	<button type="button" class="btn btn-primary" id="btn_crear_ahorrador" data-toggle="modal" data-target="#modal_reg_ahorrador" style="    margin: 3px;  ">
	  Agregar Ahorrador <i class="fi-rr-user"></i>
	</button>

	<button type="button" class="btn btn-primary" id="btn_crear_ahorro" data-toggle="modal" data-target="#modal_reg_ahorro" style="    margin: 3px;    ">
	  Agregar Ahorro  <i class="fi-rr-file-add"></i>
	</button>

	<button type="button" class="btn btn-primary" id="" id="btn_configurar_ahorro" data-toggle="modal" data-target="#modal_confi_ahorro" style="    margin: 3px;    ">
	  Configurar Ahorro <i class="fas fa-cogs"></i>
	</button>
</p>
<?php
}
?>


<hr><br>


<div>
	<label>Año:</label>
	<select id="anio_ahorro" class="form-control" style="width: 30%">
	</select>
</div>
<br>

<div id="container_ahorros" class="" style="font-size: 12px;" style="display: none;">
	<table id="tabla_ahorros" class="table table-responsive table-striped" style="width: 2000px;">
		<thead style="font-size: 15px;">
			<tr>
			</tr>
			<tr>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
