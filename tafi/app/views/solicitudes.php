<script>
    codigo_solicitud=0;

    $(document).ready(function() {
       listado_contactos_web();
       $("#btn_grabar_respuesta_solicitud").bind("click",grabar_respuesta_solicitud);
    });

    function listado_contactos_web(){
        listado_consulta("div_concatos_web","listado_contactos_web",1,1);
    }

    function registrar_aprobacion(codigo){
        codigo_solicitud=codigo;
        $("#btn_aprobacion").click();
    }

    function grabar_respuesta_solicitud(){
        var datos_formulario=$("#form_respuesta_solicitud").serialize();
        datos_formulario+="&codigo_solicitud="+codigo_solicitud;

        $.ajax({
            url: "ajax/solicitudes_grabar_respuesta.php",
            type: "POST",
            data: datos_formulario,
            success: function(respuesta) {
                if(respuesta==1){
                    alert("Se grabo correctamente");
                    $("#btn_aprobacion").click();
                    listado_contactos_web();
                }else{
                    alert("Ocurrio un error");
                }
            }
        });
        
    }
</script>

<div class="card">
    <div id="div_concatos_web" class="table-responsive"></div>    
</div>

<button type="button" style="display:none;" class="btn bg-gradient-primary" id="btn_aprobacion" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Respuesta de Solicitud</h5>

        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_respuesta_solicitud">
        <div class="form-group">
            <label for="exampleFormControlSelect1">Respuesta</label>

            <select class="form-control" id="exampleFormControlSelect1" name="codigo_estado">
                <option value="2">Aprobado</option>
                <option value="3">No Aprobado</option>
            </select>
        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-info-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn bg-gradient-info btn-block" id="btn_grabar_respuesta_solicitud">Grabar Respuesta</button>
      </div>
    </div>
  </div>
</div>