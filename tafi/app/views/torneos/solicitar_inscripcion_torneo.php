
<script>
    $(document).ready(function() {
        listado_torneos();
        listado_solicitudes();
        $("#btn_grabar_inscripcion").bind("click",grabar_inscripcion);
        rellenar_select("tbl_tafi_equipos","codigo_equipo","nombre_equipo","codigo_delegado="+codigo_origen,"codigo_equipo_sol","","nombre_equipo");
    });

    function listado_torneos(){
        listado_consulta("datos_torneos","listado_torneso_disponible_inscripcion","1",1);
    }

    function listado_solicitudes(){
        listado_consulta("listado_solicitudes","listado_solicitudes_inscripcion","sol.codigo_solicitante="+codigo_origen);
    }

    function solicitar_inscripcion(codigo_torneo){
        $("#btn_aprobacion").click();
        mostrar_consulta("div_torneo_inscripcion","listado_torneso_disponible_inscripcion","t.codigo_torneo="+codigo_torneo,1);
    }

    function grabar_inscripcion(){
        if($("#codigo_equipo_sol").val()==0){
            alert("Debe seleccionar un equipo");
            return false;
        }else{
            datos = $("#form_datos_inscripcion").serialize();

            $.ajax({
                type: 'POST',
                async: false,
                url: 'ajax/torneos/solicitud_inscripcion_torneo.php',
                data: datos,
                success: function(data){
                    alert(data);
                    $("#btn_cerrar_dialog").click();
                    listado_solicitudes();
                },
                dataType: 'text'
            });

        }
    }
</script>
<div class="card" style="padding: 13px;">
    <!--bienvenida para inscripcion a torneos-->

    <p>
        Bienvenido, en esta opción usted podrá inscribirse a los torneos que se encuentren disponibles.
    </p>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                <img src="iconos/contrato.png" class="iconos_tab">    
                Listado de Torneos Disponibles
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="equipos-tab" data-bs-toggle="tab" data-bs-target="#equipos" type="button" role="tab" aria-controls="equipos" aria-selected="false">
                <img src="iconos/tarea-completada.png" class="iconos_tab">
                Solicitudes Realizadas
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab" >
            <div id="datos_torneos" class="table-responsive" style="margin: 33px;"></div>
        </div>

        <div class="tab-pane fade" id="equipos" role="tabpanel" aria-labelledby="equipos-tab" >
            <div id="listado_solicitudes" class="table-responsive" style="margin: 33px;"></div>
        </div>

    </div>
</div>

<button type="button" style="display:none;" class="btn bg-gradient-primary" id="btn_aprobacion" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        

        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_datos_inscripcion">
            <div id="div_torneo_inscripcion"></div>

            <div class="form-group">
                <label for="exampleFormControlSelect1">Equipos</label>

                <select class="form-control" name="codigo_equipo_sol" id="codigo_equipo_sol">
                    
                </select>
            </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn bg-info-secondary" data-bs-dismiss="modal" id="btn_cerrar_dialog">Cerrar</button>
        <button type="button" class="btn bg-gradient-info btn-block" id="btn_grabar_inscripcion">Grabar Inscripción</button>
      </div>

    </div>
  </div>
</div>