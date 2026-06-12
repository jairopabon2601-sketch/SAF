<script id="tmp_sedes" type="text/template">
	<tr>
	<td>
    <input type="hidden" name="codigo_sede[]" value="0" >    
    <input class="form-control" type="text" class="fecha" name="sede[]" lang="CUR" required/></td>
    <td><input class="form-control" type="text" class="fecha" name="direccion_sede[]" lang="CUR" required/></td>
	<td><img src='iconos/eliminar.png' class='btn_eliminar_fila' style="width: 30px;"></td>
	</tr>
</script>


<script>
    codigo_solicitud=0;
    numero_sedes=0;
    const codigo_torneo=get("codigo_torneo");

    $(document).ready(function() {
       
        $("#codigo_torneo").val(codigo_torneo);
        
        cargar_sedes_torneo(codigo_torneo);
        listado_costos_torneos(codigo_torneo);
        rellenar_select("tbl_tafi_torneos_tipos_clasificacion","codigo_clasificacion","nombre","codigo_clasificacion","codigo_clasificacion","codigo_tipo_torneo=1","nombre");
        llenar_formulario("form_editar_formulario","tbl_tafi_torneos","codigo_torneo="+codigo_torneo);

        $("#btn_grabar_campeonato").bind("click",grabar_campeonato);
        $("#btn_agregar_sede").bind("click",agregar_sede);
    });

    function cargar_sedes_torneo(codigo_torneo){
        $.ajax({
            url: "ajax/torneos/listado_sedes_torneo.php",
            type: "POST",
            data:{
                codigo_torneo: codigo_torneo
            },
            dataType: "json",
            success: function(datos) {
                console.log(datos);

                if(datos.length>0){
                    $("#tbl_sedes tbody").html("");
                    $(datos).each(function(i, v) {
                        numero_sedes++;
                        $("#tbl_sedes tbody").append($("#tmp_sedes").html());
                        $("#tbl_sedes tbody tr:last-child [name='sede[]']").val(v.nombre);
                        $("#tbl_sedes tbody tr:last-child [name='direccion_sede[]']").val(v.direccion);

                        $("[name='codigo_sede[]']").eq(i).val(v.codigo_sede);

                        $(".btn_eliminar_fila").bind("click", function(){
                            if(confirm("¿Esta seguro de eliminar esta sede?")){
                                numero_sedes--;
                                eliminar_sede_registrada($(this).parent().parent(),v.codigo_sede);
                            }
                        });
                    });
                }
            }
        });
    }

    function eliminar_sede_registrada(fila,codigo_sede){
        $.ajax({
            url: "ajax/torneos/eliminar_sede_registrada.php",
            type: "POST",
            data:{
                codigo_sede: codigo_sede
            },
            dataType: "json",
            success: function(datos) {
              
                if(datos.resultado==1){
                    fila.remove();
                    cargar_sedes_torneo(codigo_torneo);
                }else{
                    alert(datos.mensaje);
                }
            }
        });
    }

    function agregar_sede(){
        numero_sedes++;
        $("#tbl_sedes tbody").append($("#tmp_sedes").html());
        $(".btn_eliminar_fila").bind("click", function(){
            numero_sedes--;
            eliminar_fecha($(this).parent().parent());
        });
    }

    function eliminar_fecha(fila){
        fila.remove();
    }

    function grabar_campeonato(){
        if(validar_cant_equipo()){
            
            if(numero_sedes==0){
                alert("Debe agregar al menos una sede");
                return false;
            } 
           
            if(validar_formulario("1","form_editar_formulario")){
                $("#form_editar_formulario").submit();
            }
        }
    }

    function validar_cant_equipo(){
        cantidad_equipos=$("#cantidad_equipos").val();
        
        if( (cantidad_equipos % 2) ==0){
            return true;
        }else{
            alert("La cantidad de equipos debe ser par");
            $("#cantidad_equipos").focus();
            return false;    
        }

    }

    function listado_costos_torneos(codigo_torneo){
        $.ajax({
            url: "ajax/listado_costos_torneo.php",
            type: "POST",
            data:{
                codigo_torneo: codigo_torneo
            },
            dataType: "json",
            success: function(datos) {

                if(datos.resultado==1){
                    tabla='<table class="table align-items-center mb-0">';
                    tabla+='<thead>';
                    tabla+='<tr>';
                        $(datos.datos).each(function(i, v) {
                            tabla+='<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">'+v.conceptos+'</th>';
                        });
                    tabla+='</tr>';
                    tabla+='</thead>';
                    tabla+='<tbody>';

                    tabla+='<tr>';
                        $(datos.datos).each(function(i, v) {

                            tabla+='<td>';
                            tabla+='<div class="input-group mb-3">';
                                tabla+='<span class="input-group-text">$</span>';
                                tabla+='<input type="text" name="costo_'+v.codigo_conceptos+'" value="'+v.valor+'" class="form-control" aria-label="Dollar amount (with dot and two decimal places)" lang="1">';
                            tabla+='</div>';
                            tabla+='</td>';
                        });
                    tabla+='</tr>';    

                    tabla+='</table>';

                    $("#div_costos_campeonatos").html(tabla);
                }else{
                   $("#div_costos_campeonatos").html(datos.mensaje);     
                }
            }
        });
        
    }
</script>

<div class="card" style="padding:20px;" id="div_registro_torneo" style="display:none;">

    <form id="form_editar_formulario" name="form_editar_formulario" enctype="multipart/form-data" action="ajax/torneos/editar_campeonato.php" method="POST">
        <input type="hidden" name="codigo_torneo" readonly>

        <h4><img src="iconos/formulario-de-contacto.png" class="img_inscripcion">Datos del Campeonato</h4>

        <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nombre del Torneo</label>
            <input class="form-control" name="nombre_torneo" type="text" value="" id="example-text-input" lang="1">
        </div>

        <div class="form-floating mb-3">
            <select class="form-control" name="codigo_clasificacion" id="codigo_clasificacion" type="text" placeholder="Enter your name..." lang="1">

            </select>   
            <label for="name">Formato de Clasificación</label>
        </div>

        <div class="form-floating mb-3">
            <select class="form-control" name="cantidad_rondas" id="cantidad_rondas" type="text" placeholder="Enter your name..." lang="1">
                <option value="1">1 Ronda</option>
                <option value="2">2 Rondas</option>
            </select>   
            <label for="name">Cantidad Rondas</label>
        </div>

        <div class="form-group">
            <label for="example-number-input" class="form-control-label">Cantidad de Equipos</label>
            <input class="form-control" name="cantidad_equipos" type="number"  id="cantidad_equipos" lang="1">
        </div>

        <div class="form-group">
            <label for="example-number-input" class="form-control-label">Cantidad de Jugadores por Equipos</label>
            <input class="form-control" name="cantidad_jugadores_equipos" type="number"  id="example-number-input" lang="1"> 
        </div>

        <div class="form-group">
            <label for="example-number-input" class="form-control-label">Cantidad de Jugadores en Cancha</label>
            <input class="form-control" name="cantidad_jugadores_cancha" type="number"  id="example-number-input" lang="1"> 
        </div>

        <div class="form-group">
            <label for="example-number-input" class="form-control-label">Fecha de Inicio</label>
            <input class="form-control" name="fecha_inicio" type="date"  id="example-number-input" lang="1">
        </div>

        <div class="form-group">
            <label for="example-number-input" class="form-control-label">Fecha Final</label>
            <input class="form-control" name="fecha_final" type="date"  id="example-number-input" lang="1">
        </div>

        <div class="form-group">
            <label for="example-number-input" class="form-control-label">Fecha limite para Inscribir Equipos</label>
            <input class="form-control" name="fecha_limite_equipos" type="date"  id="example-number-input" lang="1">
        </div>

        <div class="form-group">
            <label for="example-number-input" class="form-control-label">Fecha limite para Inscribir Jugadores</label>
            <input class="form-control" name="fecha_limite_jugadores" type="date"  id="example-number-input" lang="1">
        </div>


        <hr>
        <div>
            <h4>
                <img src="iconos/costos.png" class="img_inscripcion">
                Costos
            </h4>

            <div id="div_costos_campeonatos" class="table-responsive"></div>
        </div>

        <div>
            <h4>
                <img src="iconos/estadio.png" class="img_inscripcion">
                Sedes  <button type="button" id="btn_agregar_sede" class="btn bg-gradient-success">Agregar</button>
            </h4>
            <div class="card">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0" id="tbl_sedes">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sede</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Direccion</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                </div>
        </div>

        <button type="button" class="btn bg-gradient-info btn-block" id="btn_grabar_campeonato">Editar Torneo</button>
    </form>
</div>


<style>
    .img_inscripcion{
        width: 35px;
        height: 35px;
    }
</style>
