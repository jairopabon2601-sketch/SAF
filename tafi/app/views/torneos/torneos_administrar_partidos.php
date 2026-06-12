

<style>
  @supports (-webkit-appearance: none) or (-moz-appearance: none) {
    .checkbox-wrapper-13 input[type=checkbox] {
      --active: #275EFE;
      --active-inner: #fff;
      --focus: 2px rgba(39, 94, 254, .3);
      --border: #BBC1E1;
      --border-hover: #275EFE;
      --background: #fff;
      --disabled: #F6F8FF;
      --disabled-inner: #E1E6F9;
      -webkit-appearance: none;
      -moz-appearance: none;
      height: 21px;
      outline: none;
      display: inline-block;
      vertical-align: top;
      position: relative;
      margin: 0;
      cursor: pointer;
      border: 1px solid var(--bc, var(--border));
      background: var(--b, var(--background));
      transition: background 0.3s, border-color 0.3s, box-shadow 0.2s;
    }
    .checkbox-wrapper-13 input[type=checkbox]:after {
      content: "";
      display: block;
      left: 0;
      top: 0;
      position: absolute;
      transition: transform var(--d-t, 0.3s) var(--d-t-e, ease), opacity var(--d-o, 0.2s);
    }
    .checkbox-wrapper-13 input[type=checkbox]:checked {
      --b: var(--active);
      --bc: var(--active);
      --d-o: .3s;
      --d-t: .6s;
      --d-t-e: cubic-bezier(.2, .85, .32, 1.2);
    }
    .checkbox-wrapper-13 input[type=checkbox]:disabled {
      --b: var(--disabled);
      cursor: not-allowed;
      opacity: 0.9;
    }
    .checkbox-wrapper-13 input[type=checkbox]:disabled:checked {
      --b: var(--disabled-inner);
      --bc: var(--border);
    }
    .checkbox-wrapper-13 input[type=checkbox]:disabled + label {
      cursor: not-allowed;
    }
    .checkbox-wrapper-13 input[type=checkbox]:hover:not(:checked):not(:disabled) {
      --bc: var(--border-hover);
    }
    .checkbox-wrapper-13 input[type=checkbox]:focus {
      box-shadow: 0 0 0 var(--focus);
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch) {
      width: 21px;
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):after {
      opacity: var(--o, 0);
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):checked {
      --o: 1;
    }
    .checkbox-wrapper-13 input[type=checkbox] + label {
      display: inline-block;
      vertical-align: middle;
      cursor: pointer;
      margin-left: 4px;
    }

    .checkbox-wrapper-13 input[type=checkbox]:not(.switch) {
      border-radius: 7px;
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):after {
      width: 5px;
      height: 9px;
      border: 2px solid var(--active-inner);
      border-top: 0;
      border-left: 0;
      left: 7px;
      top: 4px;
      transform: rotate(var(--r, 20deg));
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):checked {
      --r: 43deg;
    }
  }

  .checkbox-wrapper-13 * {
    box-sizing: inherit;
  }
  .checkbox-wrapper-13 *:before,
  .checkbox-wrapper-13 *:after {
    box-sizing: inherit;
  }
</style>

<style type="text/css">
	.img_btn{
    width: 60px;
  }

  .btn{
    margin: 5px;
  }

  #div_cont_acciones{
  	display: flex !important;
    flex-wrap: nowrap !important;
    justify-content: center !important;
  }

  #tbl_resultado{
	
  }

  #nombre_fecha{
	text-align: center;:
  }

  .img_tbl{
	width: 50px;
  }
  
  .div_info_equipos{
	display: grid;
	grid-template-columns: 20% 60% 20%;
  }

  .form-control{
	width: 56px;
  }

  .div_info_jugadores{
	display: grid;
	grid-template-columns: 10% 50% 10% 10% 10% 10%;
	heigth: 50px;
  }

  .no_disponible{
	background-color: #e9ecef;
  }

  .no_disponible:focus{
	background-color: #e9ecef;
  }

  .div_dato_registrado{
	text-aling: center !important;
  }
  
</style>
<script>

	
tmp='<div class="div_info_jugadores">';
					tmp+='<div><b>DORSAL</div></b>';
					tmp+='<div><b>JUGADOR</div></b>';
					tmp+='<div><b>TITULAR</div></b>';
					tmp+='<div><img src="iconos/tarjeta-amarilla.png" class="img_tbl"></div>';
					tmp+='<div><img src="iconos/rojo.png" class="img_tbl"></div>';
					tmp+='<div><img src="iconos/gol.png" class="img_tbl"></div>';
				tmp+='</div>';

	const codigo_calendario=get("codigo_calendario");
	const key=get("key");
	let cantidad_goles_local=0;
	let cantidad_goles_visitante=0;
	let codigo_torneo=0;
	let numero_titulares=0;
	let estado_fecha=1;

	$(document).ready(function() {
		
        datos_fecha=consultar_campo("tbl_tafi_torneos_calendario","codigo_fecha, codigo_local, codigo_visitante, codigo_estado,codigo_torneo,resultado_local,resultado_visitante","codigo_calendario="+codigo_calendario);
		datos_fecha=datos_fecha.split(";");
		datos_equipos(datos_fecha);
		codigo_fecha=datos_fecha[0];
		codigo_torneo=datos_fecha[4];
		estado_fecha=datos_fecha[3];
		numero_titulares=consultar_campo("tbl_tafi_torneos","cantidad_jugadores_cancha","codigo_torneo="+codigo_torneo);

		if(estado_fecha==2){
			$("#btn_habilitar_partido").remove();
			habilitar_partido();
		}

		if(estado_fecha==3){
			$("#btn_habilitar_partido").remove();
			$("#btn_cerrar_partido").remove();
			partido_cerrado(datos_fecha);
		}

		$("#nombre_fecha").html("<h3>"+consultar_campo("tbl_tafi_torneos_calendario_fechas","nombre_fecha","codigo_fecha="+codigo_fecha)+"</h3>");

		$("#btn_habilitar_partido").bind("click",habilitar_partido);
		$("#btn_cerrar_partido").bind("click",cerrar_partido);
		$("#btn_volver_fecha").bind("click",function(){
			window.location.href="dashboard.php?proc=15";
		});

	});

	function partido_cerrado(datos_fecha){
		//marcador 
		$("#txt_local").html(datos_fecha[5]);
		$("#txt_visitante").html(datos_fecha[6]);

		campos=$("#form_registro_partido").serialize();
		campos+="&codigo_calendario="+codigo_calendario;

		$.ajax({
            type: 'POST',
            async: false,
            url: 'ajax/torneos/calendario_cerrado_partido.php',
            data: campos,
            success: function(data){

				console.log(data);

				htmll="";
				htmlv="";

				$(data.local).each(function(i, v){
					if(i==0){
						htmll+=tmp;
					}

					htmll+='<div class="div_info_jugadores">';
						htmll+='<div>'+v.dorsal+'</div>';
						htmll+='<div>'+v.jugador+'</div>';
						htmll+='<div>'+v.titular+'</div>';
						htmll+='<div class="div_dato_registrado">'+v.numero_amarillas+'</div>';
						htmll+='<div class="div_dato_registrado">'+v.numero_rojas+'</div>';
						htmll+='<div class="div_dato_registrado">'+v.numero_goles+'</div>';
					htmll+='</div>';
				});

				$(data.visitante).each(function(i, v){
					if(i==0){
						htmlv+=tmp;
					}

					htmlv+='<div class="div_info_jugadores">';
						htmlv+='<div>'+v.dorsal+'</div>';
						htmlv+='<div>'+v.jugador+'</div>';
						htmlv+='<div>'+v.titular+'</div>';
						htmlv+='<div class="div_dato_registrado">'+v.numero_amarillas+'</div>';
						htmlv+='<div class="div_dato_registrado">'+v.numero_rojas+'</div>';
						htmlv+='<div class="div_dato_registrado">'+v.numero_goles+'</div>';
					htmlv+='</div>';
				});

				if(data.local.length>data.visitante.length){
					total=data.local.length-data.visitante.length;

					for(i=0;i<total;i++){
						htmlv+='<div class="div_info_jugadores">';
							htmlv+='<div>-</div>';
							htmlv+='<div>-</div>';
							htmlv+='<div>-</div>';
							htmlv+='<div>-</div>';
							htmlv+='<div>-</div>';
							htmlv+='<div>-</div>';
						htmlv+='</div>';
					}
				}

				if(data.local.length<data.visitante.length){
					total=data.visitante.length-data.local.length;

					for(i=0;i<=total;i++){

						htmll+='<div class="div_info_jugadores">';
							htmll+='<div>-</div>';
							htmll+='<div>-</div>';
							htmll+='<div>-</div>';
							htmll+='<div>-</div>';
							htmll+='<div>-</div>';
							htmll+='<div>-</div>';
						htmll+='</div>';
					}
				}

				$("#equipo_local_resultado").html(htmll);
				$("#equipo_visitante_resultado").html(htmlv);


					
				
            },
            dataType: 'json'
        });


	}

	function datos_equipos(datos_fecha){

		//DATOS EQUIPO LOCAL
		datos_equipo_local=consultar_campo("tbl_tafi_equipos","nombre_equipo, escudo, codigo_equipo","codigo_equipo="+datos_fecha[1]);
		datos_equipo_local=datos_equipo_local.split(";");
		$("#escudo_local").html('<img src="archivos/equipos/escudos/'+datos_equipo_local[1]+'" class="img_tbl" alt="Team Logo">');
		$("#nombre_local").html('<h4>'+datos_equipo_local[0]+'<h4>');
		$("[name='codigo_equipo_local']").val(datos_equipo_local[2]);

		//DATOS EQUIPO VISITANTE
		datos_equipo_visitante=consultar_campo("tbl_tafi_equipos","nombre_equipo, escudo,codigo_equipo","codigo_equipo="+datos_fecha[2]);
		datos_equipo_visitante=datos_equipo_visitante.split(";");
		$("#escudo_visiatente").html('<img src="archivos/equipos/escudos/'+datos_equipo_visitante[1]+'" class="img_tbl" alt="Team Logo">');
		$("#nombre_visitante").html('<h4>'+datos_equipo_visitante[0]+'<h4>');
		$("[name='codigo_equipo_visitante']").val(datos_equipo_visitante[2]);
	}

	function habilitar_partido(){
		$("#btn_habilitar_partido").remove();

		$.ajax({
            type: 'POST',
            async: false,
            url: 'ajax/torneos/calendario_habilitar_partido.php',
            data: {
                codigo_calendario: codigo_calendario
            },
            success: function(data){

				htmll="";
				htmlv="";


				$(data.local).each(function(i, v){
					if(i==0){
						htmll+=tmp;
					}

					htmll+='<div class="div_info_jugadores">';
						htmll+='<div><input type="hidden" class="form-control" name="local_jugador[]" value="'+v.codigo_jugador+'">'+v.dorsal+'</div>';
						htmll+='<div>'+v.jugador+'</div>';
						htmll+='<div class="checkbox-wrapper-13"><input type="checkbox" name="local_titular[]" value="'+v.codigo_jugador+'" class="check_titular_local"></div>'; 
						htmll+='<div><input type="number" class="form-control tarjetas_amarillas" name="local_amarillas[]" value="0"></div>';
						htmll+='<div><input type="number" class="form-control tarjetas_rojas" name="local_rojas[]" value="0"></div>';
						htmll+='<div><input type="number" class="form-control goles_locales" name="local_goles[]" value="0"></div>';
					htmll+='</div>';
				});


				$(data.visitante).each(function(i, v){
					if(i==0){
						htmlv+=tmp;
					}
					htmlv+='<div class="div_info_jugadores">';
						htmlv+='<div><input type="hidden" class="form-control" name="visitante_jugador[]" value="'+v.codigo_jugador+'">'+v.dorsal+'</div>';
						htmlv+='<div>'+v.jugador+'</div>';
						htmlv+='<div class="checkbox-wrapper-13"><input type="checkbox" value="'+v.codigo_jugador+'" name="visitante_titular[]" class="check_titular_visitante"></div>';
						htmlv+='<div><input type="number" class="form-control tarjetas_amarillas" name="visitante_amarillas[]" value="0"></div>';
						htmlv+='<div><input type="number" class="form-control tarjetas_rojas" name="visitante_rojas[]" value="0"></div>';
						htmlv+='<div><input type="number" class="form-control goles_visitantes" name="visitante_goles[]" value="0"></div>';
					htmlv+='</div>';
				});

				if(data.local.length>data.visitante.length){
					total=data.local.length-data.visitante.length;

					for(i=0;i<total;i++){
						htmlv+='<div class="div_info_jugadores">';
							htmlv+='<div>-</div>';
							htmlv+='<div>-</div>';
							htmlv+='<div>-</div>';
							htmlv+='<div><input type="number" class="form-control no_disponible" readonly></div>';
							htmlv+='<div><input type="number" class="form-control no_disponible" readonly></div>';
							htmlv+='<div><input type="number" class="form-control no_disponible" readonly></div>';
						htmlv+='</div>';	
					}
				}

				if(data.local.length<data.visitante.length){
					total=data.visitante.length-data.local.length;

					for(i=0;i<=total;i++){
						htmll+='<div class="div_info_jugadores">';
							htmll+='<div>-</div>';
							htmll+='<div>-</div>';
							htmll+='<div>-</div>';
							htmll+='<div><input type="number" class="form-control no_disponible" readonly></div>';
							htmll+='<div><input type="number" class="form-control no_disponible" readonly></div>';
							htmll+='<div><input type="number" class="form-control no_disponible" readonly></div>';
						htmll+='</div>';	
					}
				}

				$("#equipo_local_resultado").html(htmll);
				$("#equipo_visitante_resultado").html(htmlv);

				validaciones_resultados();
		
            },
            dataType: 'json'
        });
	}

	function validaciones_resultados(){
		
		//validacion del numero de titulares locales 
		$(".check_titular_local").bind("click",function(){
			if($(".check_titular_local:checked").length>numero_titulares){
				$(this).prop("checked",false);
				alert("El numero de titulares no puede ser mayor a "+numero_titulares);
			}
		});
		
		//validacion del numero de titulares visitantes
		$(".check_titular_visitante").bind("click",function(){
			if($(".check_titular_visitante:checked").length>numero_titulares){
				$(this).prop("checked",false);
				alert("El numero de titulares no puede ser mayor a "+numero_titulares);
			}
		});

		//VALIDACION DE INPUNST NUMERO NO PUEDES SER MENOR DE 0 
		$("input[type=number]").bind("change",function(){
			if($(this).val()<0){
				$(this).val(0);
			}
		});

		//VALIDACION EL NUMERO DE TARJERTAS AMARIILLAS NO PUEDE SER MAYOR A 2
		$(".tarjetas_amarillas").bind("change",function(){
			if($(this).val()>2){
				$(this).val(2);
			}

			//al llegar a dos se convierte en una roja y se coloca el input readonly
			if($(this).val()==2){
				$(this).parent().parent().find(".tarjetas_rojas").val(1);
				$(this).parent().parent().find(".tarjetas_rojas").attr("readonly","readonly");
			}else{
				$(this).parent().parent().find(".tarjetas_rojas").removeAttr("readonly");
				$(this).parent().parent().find(".tarjetas_rojas").val(0);
			}
		});

		//el numero de rojas no puede ser mayor a 1
		$(".tarjetas_rojas").bind("change",function(){
			if($(this).val()>1){
				$(this).val(1);
			}
		});

		//goles equipo local 
		$(".goles_locales").bind("change",function(){
			if($(this).val()<0){
				$(this).val(0);
			}

			cantidad_goles_local=0;
			$(".goles_locales").each(function(){
				cantidad_goles_local+=parseInt($(this).val());
			});

			$("#txt_local").html(cantidad_goles_local);
			$("input[name=resultado_local]").val(cantidad_goles_local);
		});

		//goles equipo visitante
		$(".goles_visitantes").bind("change",function(){
			if($(this).val()<0){
				$(this).val(0);
			}

			cantidad_goles_visitante=0;
			$(".goles_visitantes").each(function(){
				cantidad_goles_visitante+=parseInt($(this).val());
			});

			$("#txt_visitante").html(cantidad_goles_visitante);
			$("input[name=resultado_visitante]").val(cantidad_goles_visitante);
		});
	}

	function cerrar_partido(){

		if(confirm("Confirmar el resultado del partido, una vez cerrado no se podra realizar ningun cambio")){
		//AJAX GUARDADO
		campos=$("#form_registro_partido").serialize();
		campos+="&codigo_calendario="+codigo_calendario;
			
			
		$.ajax({
            type: 'POST',
            async: false,
            url: 'ajax/torneos/calendario_cerrar_partido.php',
            data: campos,
            success: function(data){
				if(data.resultado==1){
					alert("Partido cerrado correctamente");

					/*if(data.codigo_grupo>0){
						if(data.partidos_restantes==0){
							
							
						}
					}else{*/
						window.location.href="dashboard.php?proc=15";
					//}

				}else{
					alert(data.error);
				}
				
            },
            dataType: 'json'
        });

		}
	}

</script>


<div class="card" style="padding:20px;" id="div_registro_torneo">
	<div id="div_cont_acciones" class="table-responsive">
	    <button type="button" id="btn_habilitar_partido" class="btn" onclick="">
	      <img src="iconos/tocar.png" class="img_btn"> Habilitar Partido
	    </button>

	    <button type="button" id="btn_cerrar_partido" class="btn" onclick="">
	      <img src="iconos/guardar-carpeta.png" class="img_btn">Grabar y Cerrar Partido
	    </button>

	    <button type="button" id="btn_volver_fecha" class="btn" onclick="">
	      <img src="iconos/juego.png" class="img_btn">Volver a la Fecha
	    </button>
    </div> 
</div>


<div class="card" style="padding:20px;" id="div_registro_torneo">
	<form id="form_registro_partido">
		<table id="tbl_resultado" class="table align-items-center mb-0">
			<tr >
				<td colspan="4" id="nombre_fecha">Fecha</td>
			</tr>
			<tr>
				<td id="equipo_local">
					<div class="div_info_equipos">
						<input type="hidden" name="codigo_equipo_local" >
						<div id="escudo_local" style="text-align: center;"></div>
						<div id="nombre_local"></div>
						<div id="resultado_local" style="text-align: left;"><input type="hidden" name="resultado_local" value=0><h4 id="txt_local">0</h4></div>
					</div>
				</td>
				<td id="equipo_visitante">
					<div class="div_info_equipos">
						<input type="hidden" name="codigo_equipo_visitante" >
						<div id="resultado_visitante" style="text-align: center;"><input type="hidden" name="resultado_visitante" value=0> <h4  id="txt_visitante">0</h4></div>
						<div id="nombre_visitante" style="text-align: end;"></div>
						<div id="escudo_visiatente" style="text-align: center;"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td id="equipo_local_resultado" >
					
				</td>
				<td id="equipo_visitante_resultado">
					
				</td>
			</tr>
		</table>
	</form>

</div>

<script>
	var MD5 = function(d){var r = M(V(Y(X(d),8*d.length)));return r.toLowerCase()};function M(d){for(var _,m="0123456789ABCDEF",f="",r=0;r<d.length;r++)_=d.charCodeAt(r),f+=m.charAt(_>>>4&15)+m.charAt(15&_);return f}function X(d){for(var _=Array(d.length>>2),m=0;m<_.length;m++)_[m]=0;for(m=0;m<8*d.length;m+=8)_[m>>5]|=(255&d.charCodeAt(m/8))<<m%32;return _}function V(d){for(var _="",m=0;m<32*d.length;m+=8)_+=String.fromCharCode(d[m>>5]>>>m%32&255);return _}function Y(d,_){d[_>>5]|=128<<_%32,d[14+(_+64>>>9<<4)]=_;for(var m=1732584193,f=-271733879,r=-1732584194,i=271733878,n=0;n<d.length;n+=16){var h=m,t=f,g=r,e=i;f=md5_ii(f=md5_ii(f=md5_ii(f=md5_ii(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_ff(f=md5_ff(f=md5_ff(f=md5_ff(f,r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+0],7,-680876936),f,r,d[n+1],12,-389564586),m,f,d[n+2],17,606105819),i,m,d[n+3],22,-1044525330),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+4],7,-176418897),f,r,d[n+5],12,1200080426),m,f,d[n+6],17,-1473231341),i,m,d[n+7],22,-45705983),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+8],7,1770035416),f,r,d[n+9],12,-1958414417),m,f,d[n+10],17,-42063),i,m,d[n+11],22,-1990404162),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+12],7,1804603682),f,r,d[n+13],12,-40341101),m,f,d[n+14],17,-1502002290),i,m,d[n+15],22,1236535329),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+1],5,-165796510),f,r,d[n+6],9,-1069501632),m,f,d[n+11],14,643717713),i,m,d[n+0],20,-373897302),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+5],5,-701558691),f,r,d[n+10],9,38016083),m,f,d[n+15],14,-660478335),i,m,d[n+4],20,-405537848),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+9],5,568446438),f,r,d[n+14],9,-1019803690),m,f,d[n+3],14,-187363961),i,m,d[n+8],20,1163531501),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+13],5,-1444681467),f,r,d[n+2],9,-51403784),m,f,d[n+7],14,1735328473),i,m,d[n+12],20,-1926607734),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+5],4,-378558),f,r,d[n+8],11,-2022574463),m,f,d[n+11],16,1839030562),i,m,d[n+14],23,-35309556),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+1],4,-1530992060),f,r,d[n+4],11,1272893353),m,f,d[n+7],16,-155497632),i,m,d[n+10],23,-1094730640),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+13],4,681279174),f,r,d[n+0],11,-358537222),m,f,d[n+3],16,-722521979),i,m,d[n+6],23,76029189),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+9],4,-640364487),f,r,d[n+12],11,-421815835),m,f,d[n+15],16,530742520),i,m,d[n+2],23,-995338651),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+0],6,-198630844),f,r,d[n+7],10,1126891415),m,f,d[n+14],15,-1416354905),i,m,d[n+5],21,-57434055),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+12],6,1700485571),f,r,d[n+3],10,-1894986606),m,f,d[n+10],15,-1051523),i,m,d[n+1],21,-2054922799),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+8],6,1873313359),f,r,d[n+15],10,-30611744),m,f,d[n+6],15,-1560198380),i,m,d[n+13],21,1309151649),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+4],6,-145523070),f,r,d[n+11],10,-1120210379),m,f,d[n+2],15,718787259),i,m,d[n+9],21,-343485551),m=safe_add(m,h),f=safe_add(f,t),r=safe_add(r,g),i=safe_add(i,e)}return Array(m,f,r,i)}function md5_cmn(d,_,m,f,r,i){return safe_add(bit_rol(safe_add(safe_add(_,d),safe_add(f,i)),r),m)}function md5_ff(d,_,m,f,r,i,n){return md5_cmn(_&m|~_&f,d,_,r,i,n)}function md5_gg(d,_,m,f,r,i,n){return md5_cmn(_&f|m&~f,d,_,r,i,n)}function md5_hh(d,_,m,f,r,i,n){return md5_cmn(_^m^f,d,_,r,i,n)}function md5_ii(d,_,m,f,r,i,n){return md5_cmn(m^(_|~f),d,_,r,i,n)}function safe_add(d,_){var m=(65535&d)+(65535&_);return(d>>16)+(_>>16)+(m>>16)<<16|65535&m}function bit_rol(d,_){return d<<_|d>>>32-_}
	</script>