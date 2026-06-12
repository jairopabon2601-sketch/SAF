<script id="tmp_sedes" type="text/template">
	<tr>
	<td> 
    <input class="form-control" type="text" class="fecha" name="sede[]" lang="CUR" required/></td>
    <td><input class="form-control" type="text" class="fecha" name="direccion_sede[]" lang="CUR" required/></td>
	<td><img src='iconos/eliminar.png' class='btn_eliminar_fila' style="width: 30px;"></td>
	</tr>
</script>


<script>
    codigo_solicitud=0;
    numero_sedes=0;

    $(document).ready(function() {
       listado_costos_torneos();

       $("#btn_grabar_campeonato").bind("click",grabar_campeonato);
       $("#btn_agregar_sede").bind("click",agregar_sede);

       rellenar_select("tbl_tafi_torneos_tipos_clasificacion","codigo_clasificacion","nombre","codigo_clasificacion","codigo_clasificacion","codigo_tipo_torneo=1","nombre");

       if(administrador!=1){
            permite_crear_torneo=consultar_campo("tbl_tafi_contactos_web","permite_crear_torneo","codigo='"+codigo_origen+"'");

            if(permite_crear_torneo==0){
                $("#div_registro_torneo").hide();
                $("#no_encuentra").show();
            }else{
                $("#div_registro_torneo").show();
                $("#no_encuentra").hide();
            }
       }else{
            $("#div_registro_torneo").show();
            $("#no_encuentra").hide();
       }
       
    });

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
        //Siubmit 
        if(numero_sedes==0){
            alert("Debe agregar al menos una sede");
            return false;
        } 

        if(validar_cant_equipo()){
            $("#form_crear_campeonato").submit();
        }

    }

    function validar_cant_equipo(){
        resultado=true;
        cantidad_equipos=$("#cantidad_equipos").val();
        
        if( (cantidad_equipos%2) ==0){
            resultado=true;
            return true;
        }else{
            alert("La cantidad de equipos debe ser par");
            $("#cantidad_equipos").focus();
            resultado=false;
            return false;  
        }

        return resultado;
    }

    function listado_costos_torneos(){
        $.ajax({
            url: "ajax/listado_costos_tipo_torneo.php",
            type: "POST",
            dataType: "json",
            success: function(datos) {
                console.log(datos);
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
                                tabla+='<input type="text" name="costo_'+v.codigo_conceptos+'" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">';
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

    <form id="form_crear_campeonato" name="form_crear_campeonato" enctype="multipart/form-data" action="ajax/torneos/crear_campeonato.php" method="POST">

        <h4><img src="iconos/formulario-de-contacto.png" class="img_inscripcion">Datos del Campeonato</h4>

        <div class="row">
            <!-- Columna 1 -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre_torneo" class="form-control-label">Nombre del Torneo</label>
                    <input class="form-control" name="nombre_torneo" type="text" value="" id="nombre_torneo">
                </div>

                <div class="form-group">
                    <label for="codigo_clasificacion" class="form-control-label">Formato de Clasificación</label>
                    <select class="form-control" name="codigo_clasificacion" id="codigo_clasificacion" lang="1">

                    </select>   
                </div>

                <div class="form-group">
                    <label for="cantidad_rondas" class="form-control-label">Cantidad Rondas</label>
                    <select class="form-control" name="cantidad_rondas" id="cantidad_rondas" lang="1">
                        <option value="1">1 Ronda</option>
                        <option value="2">2 Rondas</option>
                    </select>   
                </div>

                <div class="form-group">
                    <label for="cantidad_equipos" class="form-control-label">Cantidad de Equipos</label>
                    <input class="form-control" name="cantidad_equipos" id="cantidad_equipos" type="number">
                </div>

                <div class="form-group">
                    <label for="cantidad_jugadores_equipos" class="form-control-label">Cantidad de Jugadores por Equipos</label>
                    <input class="form-control" name="cantidad_jugadores_equipos" id="cantidad_jugadores_equipos" type="number">
                </div>
            </div>

            <!-- Columna 2 -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cantidad_jugadores_cancha" class="form-control-label">Cantidad de Jugadores en Cancha</label>
                    <input class="form-control" name="cantidad_jugadores_cancha" id="cantidad_jugadores_cancha" type="number" lang="1"> 
                </div>

                <div class="form-group">
                    <label for="fecha_inicio" class="form-control-label">Fecha de Inicio</label>
                    <input class="form-control" name="fecha_inicio" id="fecha_inicio" type="date">
                </div>

                <div class="form-group">
                    <label for="fecha_final" class="form-control-label">Fecha Final</label>
                    <input class="form-control" name="fecha_final" id="fecha_final" type="date">
                </div>

                <div class="form-group">
                    <label for="fecha_limite_equipos" class="form-control-label">Fecha limite para Inscribir Equipos</label>
                    <input class="form-control" name="fecha_limite_equipos" id="fecha_limite_equipos" type="date">
                </div>

                <div class="form-group">
                    <label for="fecha_limite_jugadores" class="form-control-label">Fecha limite para Inscribir Jugadores</label>
                    <input class="form-control" name="fecha_limite_jugadores" id="fecha_limite_jugadores" type="date">
                </div>
            </div>
        </div>


        <hr>
        <div>
            <h4>
                <img src="iconos/costos.png" class="img_inscripcion">
                Costos
            </h4>

            <div id="div_costos_campeonatos"></div>
        </div>

        <div>
            <h4>
                <img src="iconos/resultados.png" class="img_inscripcion">
                Reglamento (PDF)
            </h4>
            

            <div class="form-group">
                <input class="form-control" name="reglamento" type="file"  id="example-number-input">
            </div>
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

        <button type="button" class="btn bg-gradient-info btn-block" id="btn_grabar_campeonato">Crear Torneo</button>
    </form>
</div>

<div class="card"     id="no_encuentra" style="display:none;">
    <h4>No tiene permisos para crear campeonatos</h4>
</div>

<style>
    .img_inscripcion{
        width: 35px;
        height: 35px;
    }
</style>
