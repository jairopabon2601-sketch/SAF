<script>  
    const codigo_inscripcion=get("codigo");
    datos=consultar_campo("tbl_tafi_torneos_equipos","codigo_torneo, codigo_equipo","codigo_torneo_equipo='"+codigo_inscripcion+"'");
    datos=datos.split(";");

    const codigo_torneo=datos[0];
    const codigo_equipo=datos[1];

    $(document).ready(function() {
       listado_equipos();
    });

    function listado_equipos(){
        const filtro="del.codigo_delegado='"+codigo_origen+"' and tor_eq.codigo_torneo_equipo='"+codigo_inscripcion+"'";
        mostrar_consulta("div_datos_torneo","listado_equipos_torneos",filtro,1);
    }

</script>

<div class="card">
    <div id="div_datos_torneo" class="table-responsive"></div>    
    <div>
	    <ul class="nav nav-tabs" id="myTab" role="tablist">
	        <li class="nav-item" role="presentation">
	            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
	                <img src="iconos/grafico.png" class="iconos_tab">    
	            	Estadisticas
	            </button>
	        </li>

	        <li class="nav-item" role="presentation">
	            <button class="nav-link" id="inscribir-tab" data-bs-toggle="tab" data-bs-target="#inscribir" type="button" role="tab" aria-controls="inscribir" aria-selected="false">
	            <img src="iconos/inscripcion.png" class="iconos_tab">
                    Inscripción de Jugadores
	            </button>
	        </li>
	    </ul>
    
	    <div class="tab-content" id="myTabContent">
	        
	        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab" >
	            <div id="listado_jugadores" style="margin: 33px;"></div>
	        </div>

	        <div class="tab-pane fade" id="inscribir" role="tabpanel" aria-labelledby="inscribir-tab">
	            <div id="reg_jugadores" style="">

				</div>
	        </div>

	     
	    </div>
    </div>
</div>
