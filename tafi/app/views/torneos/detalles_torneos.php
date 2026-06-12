   

<script>
    codigo_solicitud=0;
    const codigo_torneo=get("codigo_torneo");

    $(document).ready(function() {

        $("#imp_codigo_torneo").val(codigo_torneo);
        $("#btn_registrar_delegado").bind("click",registrar_delegado);
        $("#btn_grabar_aprobacion").bind("click",grabar_aprobacion);
        $("#btn_grabar_registro_abono").bind("click",grabar_registro_abono);
        $("#btn_cambiar_reglamento").bind("click",function(){
            $("#form_cambiar_reglamento").submit();
        });

        listado_aprobaciones();
        datos_torneo();
        datos_costos();
        listado_equipos();

        $(".btn_consulta_abono").bind("click",function(){
            codigo_torneo_equipo=$(this).attr("rel");
            filtro="codigo_torneo_equipo="+codigo_torneo_equipo;

            listado_consulta("div_historial_abono","listado_abonos_inscripcion",filtro);
            $("#btn_historial_abono").click();
        });
        
        cargar_reglamento();
    });
    
    function cargar_reglamento(){
        var url = "reglamentos/reglamento_"+codigo_torneo+".pdf?v="+Math.random();
        // Try a HEAD request to verify existence
        $.ajax({
            url: url,
            type: 'HEAD',
            cache: false
        }).done(function(){
            $("#if_reglamento").attr("src", url).show();
            $("#reglamento_msg").hide();
        }).fail(function(){
            // If HEAD not allowed or 404, show message and hide iframe
            $("#if_reglamento").hide().attr("src", "");
            $("#reglamento_msg").text("No se encontró el reglamento para este torneo.").show();
        });
    }

    function listado_aprobaciones(){
        filtro="sol.codigo_torneo="+codigo_torneo;
        listado_consulta("datos_aprobaciones","listado_solicitudes_inscripcion_pendiente",filtro ,1,false);
    }

    function registrar_aprobacion(codigo_solicitud,codigo_torneo,codigo_equipo){
        $("#codigo_solicitud").val(codigo_solicitud);
        $("#codigo_solicitud_torneo").val(codigo_torneo);
        $("#codigo_solicitud_equipo").val(codigo_equipo);

        const nombre_equipo=consultar_campo("tbl_tafi_equipos","nombre_equipo","codigo_equipo='"+codigo_equipo+"'");
	    const escudo=consultar_campo("tbl_tafi_equipos","escudo","codigo_equipo='"+codigo_equipo+"'");

        if (escudo=="") {
    		$("#img_equipo").attr("src","archivos/equipos/escudos/escudo-deportivo.png");
    	}else{
    		$("#img_equipo").attr("src","archivos/equipos/escudos/"+escudo);
    	}

        listado_jugadores_equipo(codigo_equipo);
    	
        $("#h2_nombre_equipo").html(nombre_equipo);

        $("#btn_modal_aprobacion").click();
    }

    function grabar_aprobacion(){
        if(validar_formulario("1","form_registro_aprobacion")){
            campos=$("#form_registro_aprobacion").serialize();

            $.ajax({
                url: "ajax/torneos/solicitudes_grabar_respuesta.php",
                type: "POST",
                data: campos,
                dataType: "text",
                success: function(respuesta) {
                    alert(respuesta);
                    $("#form_registro_aprobacion")[0].reset();
                    $("#btn_cerrar_aprobacion").click();
                    listado_aprobaciones();
                    listado_equipos();
                }
            });
        }
    }

    function listado_jugadores_equipo(codigo_equipo){
		
        $.ajax({
            url: "ajax/equipos_listado_jugadores.php",
            type: "POST",
			dataType: "json",
            data: {
				codigo_equipo: codigo_equipo
			},
            success: function(res) {

				posicion="";
				html="";
                if (res.resultado == 1) {
					i=0;
					$.each(res.datos, function(i, item) {

						if (posicion!=item.posicion) {
							if(i>0){
								html+='</div><br>';	
							}

							html+="<h3>"+item.posicion+"</h3>";
							posicion=item.posicion;
							html+='<div style="display: flex;">';
						}

						html+='<a class="btn_hoja_vida" codigo_jugador="'+item.codigo_jugador+'"><div class="card_jug">';	
							html+='<div class="profileImage">';
								html+='<img src="archivos/jugadores/fotos/'+item.ruta_foto+'" style="width:100%;z-index: 1;" alt="Messi" draggable="false">';
							html+='</div>';
							html+='<div class="textContainer">';
								html+='<p class="name">'+item.dorsal+"."+item.jugador+'</p>';
								html+='<p class="profile">'+item.numero_documento+'</p>';
							html+='</div>';
						html+='</div><a>';
						i++;
					});
					
					$("#div_plantilla_jugadores").html(html);

					$(".btn_hoja_vida").bind("click",function(){
						codigo_jugador=$(this).attr("codigo_jugador");
						window.open("dashboard.php?proc=12&codigo_jugador="+codigo_jugador,"_blank");
					});
    	
				} else {
					$("#div_plantilla_jugadores").html(res.mensaje);
				}
            }
        });	

	}


    function listado_equipos(){
        filtro="te.codigo_torneo="+codigo_torneo;
        listado_consulta("listado_equipos","listado_torneos_equipos",filtro ,"1",false);
    }

    function listado_jugadores(codigo_equipo){
        $("#btn_listado_jugadores").click();

        $("#div_consulta_plantilla").html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');

        $.ajax({
            url: "ajax/equipos_listado_jugadores.php",
            type: "POST",
			dataType: "json",
            data: {
				codigo_equipo: codigo_equipo
			},
            success: function(res) {

				posicion="";
				html="";
                if (res.resultado == 1) {
					i=0;
					$.each(res.datos, function(i, item) {

						if (posicion!=item.posicion) {
							if(i>0){
								html+='</div><br>';	
							}

							html+="<h3>"+item.posicion+"</h3>";
							posicion=item.posicion;
							html+='<div style="display: flex;">';
						}

						html+='<a class="btn_hoja_vida" codigo_jugador="'+item.codigo_jugador+'"><div class="card_jug">';	
							html+='<div class="profileImage">';
								html+='<img src="archivos/jugadores/fotos/'+item.ruta_foto+'" style="width:100%;z-index: 1;" alt="Messi" draggable="false">';
							html+='</div>';
							html+='<div class="textContainer">';
								html+='<p class="name">'+item.dorsal+"."+item.jugador+'</p>';
								html+='<p class="profile">'+item.numero_documento+'</p>';
							html+='</div>';
						html+='</div><a>';
						i++;
					});
					
					$("#div_consulta_plantilla").html(html);

					$(".btn_hoja_vida").bind("click",function(){
						codigo_jugador=$(this).attr("codigo_jugador");
						window.open("dashboard.php?proc=12&codigo_jugador="+codigo_jugador,"_blank");
					});
    	
				} else {
					$("#div_consulta_plantilla").html(res.mensaje);
				}
            }
        });	
    }

    function datos_torneo(){
        mostrar_consulta("datos_torneos","listado_torneos","t.codigo_torneo="+codigo_torneo);
    }

    function datos_costos(){
        listado_consulta("datos_costos","listado_costos","t.codigo_torneo="+codigo_torneo);
    }

    function registrar_delegado(){

        if(validar_formulario("1","form_delegado")){
           
            var datos_formulario=$("#form_delegado").serialize();
            datos_formulario+="&codigo_torneo="+codigo_torneo;

            $.ajax({
                url: "ajax/registrar_delegado.php",
                type: "POST",
                data: datos_formulario,
                dataType: "json",
                success: function(rest) {
                    if(rest.respuesta==1){
                        alert(rest.mensaje);

                        window.location.href="dashboard.php?proc=4";
                    }else{
                        alert(rest.mensaje);
                    }
                }
            });
        }
    }

    function registrar_abono(codigo_torneo_equipo){
        $("#btn_registro_abono").click();
        $("#codigo_torneo_equipo").val(codigo_torneo_equipo);
    }

    function grabar_registro_abono(){
        if(validar_formulario("1","form_registro_abono")){
            campos=$("#form_registro_abono").serialize();

            $.ajax({
                url: "views/torneos/ajax/registro_abono_inscripcion.php",
                type: "POST",
                data: campos,
                dataType: "text",
                success: function(respuesta) {
                    alert(respuesta);
                    $("#form_registro_abono")[0].reset();
                    $("#btn_registro_abono").click();
                    listado_equipos();
                }
            });
        }
    }
 
</script>

<div class="card">
    <div id="datos_torneos" class="table-responsive"></div>    

    <div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ins-tab" data-bs-toggle="tab" data-bs-target="#ins" type="button" role="tab" aria-controls="ins" aria-selected="true">
                <img src="iconos/comprobado.png" class="iconos_tab">    
            Aprobación de Inscripción
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                <img src="iconos/costos.png" class="iconos_tab">    
            Costos
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="equipos-tab" data-bs-toggle="tab" data-bs-target="#equipos" type="button" role="tab" aria-controls="equipos" aria-selected="false">
                <img src="iconos/unido.png" class="iconos_tab">
                Equipos Inscritos
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#delegado" type="button" role="tab" aria-controls="delegado" aria-selected="false">
                <img src="iconos/delegar.png" class="iconos_tab">
                Crear Delegado
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                <img src="iconos/resultados.png" class="iconos_tab">
                Reglamento
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="ins" role="tabpanel" aria-labelledby="ins-tab" >
            <div id="datos_aprobaciones" class="table-responsive" style="margin: 33px;"></div>
        </div>
        
        <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab" >
            <div id="datos_costos" class="table-responsive" style="margin: 33px;"></div>
        </div>

        <div class="tab-pane fade" id="equipos" role="tabpanel" aria-labelledby="equipos-tab">
            <div id="listado_equipos" class="table-responsive" style="margin: 33px;"></div>
        </div>
        
        <div class="tab-pane fade" id="delegado" role="tabpanel" aria-labelledby="delegado-tab">
            <form id="form_delegado" name="form_delegado">
                <div style="margin: 33px;">
                    
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Nombre del delegado</label>
                        <input class="form-control" name="nombre_delegado" type="text" value="" id="example-text-input" lang="1">
                    </div>

                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">N° Documento</label>
                        <input class="form-control" name="numero_documento" type="text" value="" id="example-text-input" lang="1">
                    </div>

                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Email</label>
                        <input class="form-control" name="email" type="email" value="" id="example-text-input" lang="1">
                    </div>

                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">Celular</label>
                        <input class="form-control" name="celular" type="text" value="" id="example-text-input" lang="1">
                    </div>

                    <button class="btn bg-gradient-info" type="button" id="btn_registrar_delegado">
                        Registrar Delegado             
                    </button>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">

            <div style="margin: 33px;">
                <button type="button" class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Cambiar Reglamento
                </button>

                <iframe src="" id="if_reglamento" style="width: 100%;height: 700px;"></iframe>
                <div id="reglamento_msg" class="alert alert-warning" style="display:none; margin-top: 10px;"></div>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> <img src="iconos/resultados.png" class="img_inscripcion"> Cambiar Reglamento</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">

            <form id="form_cambiar_reglamento" name="form_cambiar_reglamento" enctype="multipart/form-data" action="ajax/cambiar_reglamento.php" method="POST">
                <input type="hidden" name="codigo_torneo" id="imp_codigo_torneo">

                <h5>
                    Reglamento (PDF)
                </h5>
                
                <div class="form-group">
                    <input class="form-control" name="reglamento" type="file"  id="example-number-input">
                </div>
            </form> 
    
        </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn bg-gradient-info" id="btn_cambiar_reglamento">Grabar Cambio</button>
      </div>
    </div>
  </div>
</div>

<button type="button" class="btn btn-outline-info" id="btn_modal_aprobacion" data-bs-toggle="modal" data-bs-target="#modal_aprobacion" style="display:none;"></button>

<div class="modal fade" id="modal_aprobacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> <img src="iconos/comprobado.png" class="img_inscripcion">Registro de Aprobación</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">

            <form id="form_registro_aprobacion" name="form_registro_aprobacion" enctype="multipart/form-data" action="ajax/cambiar_reglamento.php" method="POST">
                <input type="hidden" name="codigo_solicitud" id="codigo_solicitud">   
                <input type="hidden" name="codigo_solicitud_equipo" id="codigo_solicitud_equipo">   
                <input type="hidden" name="codigo_solicitud_torneo" id="codigo_solicitud_torneo">     
            
                <div class="card_eq">
                    <img src="" id="img_equipo">
                    <h2 id="h2_nombre_equipo"></h2>
                </div>

                <p>
                    <a class="btn bg-gradient-info" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Ver Plantilla
                    </a>
                </p>
                <div class="collapse" id="collapseExample">
                    <div class="card card-body" id="div_plantilla_jugadores">
                        
                    </div>
                </div>

                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Resultado Aprobación</label>
                    <select class="form-control" name="codigo_estado"  value="" id="codigo_estado" lang="1">
                        <option value="2">Aprobado</option>
                        <option value="3">No Aprobado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Respuesta</label>
                    <textarea class="form-control" name="respuesta"  value="" id="respuesta" lang="1"></textarea>
                </div>

            </form> 
    
        </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" id="btn_cerrar_aprobacion" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn bg-gradient-info" id="btn_grabar_aprobacion">Grabar Aprobación</button>
      </div>
    </div>
  </div>
</div>

<a></a>

<!--BTN REGISTRO ABONO-->
<button type="button" class="btn bg-gradient-info" data-bs-toggle="modal" id="btn_registro_abono" data-bs-target="#registro_abono_inscripcion" style="display:none;"></button>

<div class="modal fade" id="registro_abono_inscripcion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="width: 50%;">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> <img src="iconos/resultados.png" class="img_inscripcion"> Registro de Abonos (INSCRIPCIÓN)</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

        <div class="modal-body">

            <form id="form_registro_abono" name="form_registro_abono" enctype="multipart/form-data" action="ajax/cambiar_reglamento.php" method="POST">
                <input type="hidden" name="codigo_torneo_equipo" id="codigo_torneo_equipo">

                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Valor de Abono</label>
                    <input class="form-control" name="valor_abono" type="text" value="" id="example-text-input" lang="1">
                </div>

                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Fecha de Pago</label>
                    <input class="form-control" name="fecha_pago" type="date" value="" id="example-text-input" lang="1">
                </div>
                
            </form> 
    
        </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn bg-gradient-info" id="btn_grabar_registro_abono">Grabar Abono</button>
      </div>
    </div>
  </div>
</div>


<!--BTN HISTORIAL ABONO-->

<button type="button" class="btn bg-gradient-info" data-bs-toggle="modal" id="btn_historial_abono" data-bs-target="#historial_abono" style="display:none;"></button>

<div class="modal fade" id="historial_abono" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="width: 50%;">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" > <img src="iconos/resultados.png" class="img_inscripcion"> Historial de Abonos (INSCRIPCIÓN)</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

        <div class="modal-body">
           <div id="div_historial_abono"></div>
        </div>

      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!--BTN jugadores-->
<button type="button" class="btn bg-gradient-info" data-bs-toggle="modal" id="btn_listado_jugadores" data-bs-target="#listado_jugadores" style="display:none;"></button>

<div class="modal fade" id="listado_jugadores" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="width: 80%;">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" > <img src="iconos/cv.png" class="img_inscripcion"> Plantilla</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

        <div class="modal-body">
           <div id="div_consulta_plantilla"></div>
        </div>

      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<style>

    .btn_consulta_abono{
        color: #005fff;
    }

    .modal-dialog {
        max-width: 90% !important;
    }

    .iconos_tab{
        width: 35px;
        height: 35px;
    }

    .img_inscripcion{
        width: 35px;
        height: 35px;
    }

    .card_eq{
		margin: auto;	
		display: flex;
	    margin: auto;
	    flex-direction: column;
	    align-content: center;
	    flex-wrap: nowrap;
	    justify-content: center;
	    align-items: center;
	}

	.card_eq img {
		margin-top: 30px;	
		width: 9%;
	}

	.card_eq h2{
		background: linear-gradient(#7bc7eb,#00147a);
	    -webkit-background-clip: text;
	    color: transparent;
	}

    .card_jug {
		width: 210px;
		height: 280px;
		background: rgb(44 43 76);
		border-radius: 12px;
		box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.123);
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: flex-start;
		transition-duration: .5s;
		margin : 5px;
	}

	.card_jug:hover {
	transform: translateY(-1.2rem);
	border: #f2295bf0 0.2em solid;
	border-radius: 2.5rem 0 2.5rem 0;
	}

	.profileImage img {
	background: linear-gradient(to right,rgb(54, 54, 54),rgb(32, 32, 32));
	margin-top: 20px;
	width: 150px;
	height: 150px;
	border-radius: 50%;
	box-shadow: 5px 10px 20px rgba(0, 0, 0, 0.329);
	object-fit: cover;
	}

	.textContainer {
	width: 100%;
	text-align: left;
	padding: 20px;
	display: flex;
	flex-direction: column;
	gap: 10px;
	}

	.name {
	font-size: 0.9em;
	font-weight: 600;
	color: white;
	letter-spacing: 0.5px;
	}

	.profile {
	font-size: 0.84em;
	color: rgb(194, 194, 194);
	letter-spacing: 0.2px;
	margin-top: -23px;
	}
</style>