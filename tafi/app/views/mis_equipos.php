<script>  
    const permite_crear_equipo = consultar_campo("tbl_tafi_delegados","permite_crear_equipo","codigo_delegado='"+codigo_origen+"'");   
    let codigo_equipo_editar=0;

    $(document).ready(function() {
       listado_equipos();

       if(permite_crear_equipo==0){
            $("#btn_crear_equipo").remove();
       }else{
            $("#btn_crear_equipo").show();
       }

       $("#btn_grabar_equipo").bind("click",grabar_equipo);
       $("#btn_grabar_editar_equipo").bind("click",grabar_edicion_equipo);
    });

    function listado_equipos(){
        const filtro="del.codigo_delegado='"+codigo_origen+"'";

        listado_consulta("div_equipos","listado_equipos",filtro,1);
    }

    function detalles_equipos(codigo){
        window.location.href= "dashboard.php?proc=9&codigo_equipo="+codigo;
    }

    function editar_equipos(codigo){
      codigo_equipo_editar=codigo;
      llenar_formulario("form_editar_equipo","tbl_tafi_equipos","codigo_equipo="+codigo);
      $("#btn_editar_equipo").click();

    }

    function grabar_edicion_equipo(){
      if($("#txt_nombre_equipo").val()==""){
        alert("Debe ingresar el nombre del equipo.");
      }else{

        $.ajax({
          url: "ajax/equipos/equipos_editar_equipo.php",
          type: "POST",
          data: {
            codigo_equipo: codigo_equipo_editar, 
            nombre_equipo: $("#txt_nombre_equipo").val()
          },
          dataType: "text",
          success: function(respuesta) {
            alert(respuesta);
            listado_equipos();
            $("#btn_cerrar_edicion").click();
          }
        });
      }
    }

    function grabar_equipo(){
        if(permite_crear_equipo==1){
            $("#form_crear_equipo").submit();
        }else{
            alert("No tiene permisos para crear equipos");
        }
    }

</script>

<button type="button" id="btn_editar_equipo" style="display:none;"  class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#modalEditarEquipo">
    Editar Equipo
</button>

<div class="card">
    <div style="margin: 9px;">
        <button type="button" id="btn_crear_equipo" style="display:none;"  class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Crear Equipo
        </button>
    </div>
    

    <div id="div_equipos" class="table-responsive"></div>    
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Creación de Equipos</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form id="form_crear_equipo" name="form_crear_equipo" enctype="multipart/form-data" action="ajax/equipos_crear_equipos.php" method="POST">
                
                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Nombre del Equipo</label>
                    <input class="form-control" name="nombre_equipo" type="text" value="" id="example-text-input">
                </div>

                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">logo</label>
                    <input class="form-control" name="logo" type="file"  id="example-number-input">
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn bg-gradient-primary" id="btn_grabar_equipo">Crear Equipo</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="modalEditarEquipo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Editar Equipo</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
            <form id="form_editar_equipo" name="form_editar_equipo" enctype="multipart/form-data" action="ajax/equipos_crear_equipos.php" method="POST">
                
                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Nombre del Equipo</label>
                    <input class="form-control" name="nombre_equipo" type="text" value="" id="txt_nombre_equipo">
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal" id="btn_cerrar_edicion">Cerrar</button>
        <button type="button" class="btn bg-gradient-primary" id="btn_grabar_editar_equipo">Editar Equipo</button>
      </div>
    </div>
  </div>
</div>
