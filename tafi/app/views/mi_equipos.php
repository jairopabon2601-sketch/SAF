<script>
	const codigo_equipo=get("codigo_equipo");

	const nombre_equipo=consultar_campo("tbl_tafi_equipos","nombre_equipo","codigo_equipo='"+codigo_equipo+"'");
	const escudo=consultar_campo("tbl_tafi_equipos","escudo","codigo_equipo='"+codigo_equipo+"'");

    $(document).ready(function() {

		$("#cod_eq_jug").val(codigo_equipo);
		$("#codigo_equipo_escudo").val(codigo_equipo);

		rellenar_select("tbl_tafi_tipo_documento","codigo_tipo_documento","tipo","codigo_tipo_documento","tipo_documento","","tipo");
		rellenar_select("tbl_tafi_jugadores_posicion","codigo_posicion","nombre","codigo_posicion","codigo_posicion","","orden");

		listado_jugadores_equipo();

    	if (escudo=="") {
    		$("#img_equipo").attr("src","archivos/equipos/escudos/escudo-deportivo.png");
    	}else{
    		$("#img_equipo").attr("src","archivos/equipos/escudos/"+escudo);
    	}

    	$("#h2_nombre_equipo").html(nombre_equipo);

		$("#btn_grabar_jugador").bind("click",grabar_jugador);
		$("#btn_cambiar_escudo").bind("click",function(){
			if($("#logo").val()!=""){
				$("#form_cambiar_logo").submit();
			}else{
				alert("Debe seleccionar un archivo");
			}
		});
    });

   	function listado_jugadores_equipo(){
		
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
							html+='<div style="display: flex;" 	>';
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
					
					$("#div_listado_jugadores").html(html);

					$(".btn_hoja_vida").bind("click",function(){
						codigo_jugador=$(this).attr("codigo_jugador");
						window.open("dashboard.php?proc=12&codigo_jugador="+codigo_jugador,"_blank");
					});
    	
				} else {
					$("#div_listado_jugadores").html(res.mensaje);
				}
            }
        });	

	}

	function grabar_jugador(){
		if(validar_formulario("1","form_crear_juagador")){
			$("#form_crear_juagador").submit();
		}
	}

</script>


<div class="card">
	<div class="card_eq">
		<div id="div_img">
			<img src="" id="img_equipo">
		</div><br>
	    <h2 id="h2_nombre_equipo"></h2>
	</div>

	 <div>
	    <ul class="nav nav-tabs" id="myTab" role="tablist">
	        <li class="nav-item" role="presentation">
	            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
	                <img src="iconos/equipo-de-futbol.png" class="iconos_tab">    
	            Listado de Jugadores
	            </button>
	        </li>

	        <li class="nav-item" role="presentation">
	            <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#cambiar_logo" type="button" role="tab" aria-controls="cambiar_logo" aria-selected="true">
	                <img src="iconos/intercambiar.png" class="iconos_tab">    
	            	Cambiar Escudo
	            </button>
	        </li>

	    </ul>
    
	    <div class="tab-content" id="myTabContent">
	        
	        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab" >
	            <div id="" style="margin: 33px;">
					<button type="button" id="btn_crear_equipo" class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#exampleModal">
						Crear Jugador
					</button>

					<div id="div_listado_jugadores"></div>
				</div>
	        </div>

	        <div class="tab-pane fade" id="cambiar_logo" role="tabpanel" aria-labelledby="inscribir-tab">
	            <div id="listado_equipos" style="margin: 33px;">	

					<form  id="form_cambiar_logo" name="form_cambiar_logo" enctype="multipart/form-data" action="ajax/equipos_cambiar_escudo.php" method="POST">
						<input name="codigo_equipo_escudo" id="codigo_equipo_escudo" type="hidden">  

						<div class="form-group">
							<label for="example-text-input" class="form-control-label">Escudo (Se recomienda que sea formato.PNG)</label>
							<input class="form-control" name="logo" id="logo" type="file"  id="example-number-input" style="width: 500px;margin-bottom: 10px;">

							<button class="btn bg-gradient-info" type="button" id="btn_cambiar_escudo">
								Grabar Cambio
							</button>
						</div>

					</form>
				</div>
	        </div>
	     
	    </div>
    </div>

</div>

<!-------MODAL CREAR JUGADOR-------->

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Creación de Jugador</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="height: 300px;overflow-y: scroll;">
            <form id="form_crear_juagador" name="form_crear_juagador" enctype="multipart/form-data" action="ajax/jugador/jugador_registro.php" method="POST">
				
				<input type="hidden" name="codigo_equipo_jugador" id="cod_eq_jug">

				<div class="form-floating mb-3">
					<select class="form-control" name="tipo_documento" id="tipo_documento" type="text" placeholder="Enter your name..." lang="1">

					</select>   
					<label for="name">Tipo de Documento</label>
				</div>

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">N° Documento</label>
                    <input class="form-control" name="numero_documento" type="text" value="" id="example-text-input" lang="1">
                </div>

                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Nombres</label>
                    <input class="form-control" name="nombres" type="text" value="" id="example-text-input" lang="1">
                </div>

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">Apellidos</label>
                    <input class="form-control" name="apellidos" type="text" value="" id="example-text-input" lang="1">
                </div>

				<div class="form-group">
					<label for="example-text-input" class="form-control-label">Fecha de Nacimiento</label>
					<input class="form-control" name="fecha_nacimiento" type="date" value="" id="example-text-input" lang="1">
				</div>

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">Email</label>
                    <input class="form-control" name="email" type="text" value="" id="example-text-input" lang="1">
                </div>

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">Celular</label>
                    <input class="form-control" name="celular" type="text" value="" id="example-text-input" lang="1">
                </div>

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">Eps</label>
                    <input class="form-control" name="eps" type="text" value="" id="example-text-input" lang="1">
                </div>

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">Dorsal</label>
                    <input class="form-control" name="dorsal" type="number" value="" id="example-text-input" lang="1">
                </div>

				<div class="form-floating mb-3">
					<select class="form-control" name="codigo_posicion" id="codigo_posicion" type="text" placeholder="Enter your name..." lang="1">

					</select>   
					<label for="name">Posición</label>
				</div>

				<h4>Adjuntos</h4>

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">Doc. Identidad (PDF)</label>
                    <input class="form-control" name="doc_identidad" type="file" value="" id="doc_identidad" lang="1">
                </div>	

				<div class="form-group">
                    <label for="example-text-input" class="form-control-label">Foto (PNG)</label>
                    <input class="form-control" name="foto" type="file" value="" id="foto" lang="1">
                </div>	

            </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn bg-gradient-primary" id="btn_grabar_jugador">Crear Jugador</button>
      </div>
    </div>
  </div>
</div>


<style>
	#div_img{
		width: 160px;
		height : 160px;
		display: flex;
		align-content: flex-start;
		align-items: center;
		justify-content: space-between;
		flex-wrap: nowrap;

	}	

	#img_equipo{
		width: 100%;
		object-fit: cover;
	}

	.iconos_tab{
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
		width: 200px;
		height: 240px;
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
		width: 110px;
		height: 110px;
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
