<script>
    codigo_solicitud=0;
   
    $(document).ready(function() {
       listado_contactos_web();

       $("#btn_calendario").bind("click",function(){
            window.location.href= "dashboard.php?proc=14";
       });

       $("#tbl_registro_resultados").bind("click",function(){
            window.location.href= "dashboard.php?proc=15";
       });

        $("#tbl_clasificacion").bind("click",function(){
              window.location.href= "dashboard.php?proc=17";
        });
    });

    function listado_contactos_web(){
        filtro="";
        union="";
        
        if(administrador!=1){
          filtro+=union +" c.codigo="+codigo_origen;
        }
        
        /*$.ajax({
          url: "views/torneos/ajax/torneos_listados.php",
          type: "POST",
          dataType: "json",
          data: { filtro: filtro },
          success: function(response) {
            console.log(response);
          }
        });*/

        listado_consulta("div_concatos_web","listado_torneos",filtro,1);
    }

    function detalles_torneo(codigo){
        window.location.href= "dashboard.php?proc=5&codigo_torneo="+codigo;
    }

    function editar_torneo(codigo){
        window.location.href= "dashboard.php?proc=7&codigo_torneo="+codigo;
    }

    function editar_torneo(codigo){
        window.location.href= "dashboard.php?proc=7&codigo_torneo="+codigo;
    }
</script>

<div class="card">
    <div id="" class="table-responsive">
    
    <button type="button" id="btn_calendario" class="btn" onclick="">
      <img src="iconos/calendario.png" class="img_btn"> Calendario
    </button>

    
    <button type="button" id="tbl_registro_resultados" class="btn" onclick="">
      <img src="iconos/juego.png" class="img_btn"> Registro de Resultados
    </button>

    <button type="button" id="tbl_clasificacion" class="btn" onclick="">
      <img src="iconos/podio.png" class="img_btn"> Clasificación
    </button>

    </div>    
</div>
<br>
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

<style>
  .img_btn{
    width: 60px;
  }

  .btn{
    margin: 5px;
  }
</style>