<script src="views/torneos/js/formato_grupo.js?v=20243101"></script>
<script src="views/torneos/js/formato_todos_contra_todos.js?v=2323"></script>

<link rel="stylesheet" href="views/torneos/css/registro_calendario.css?v=5221">

<script>
    codigo_torneo = get("codigo_torneo");
    let sedes="";
    let fechas_calendario=0;
    
    $(document).ready(function(){
        deplegableSedes();
        validar();
        $("#codigo_clasificacion").change(function(){
           
            if($(this).val()==2){
                $("#div_cant_equipos").show();
                $("[name='cantidad_equipos_grupos']").val("").attr("lang",1);
            }else{
                $("#div_cant_equipos").hide();
                $("[name='cantidad_equipos_grupos']").val("").attr("lang",0);

            }
        });

        $("#btn_cargar_calendario").bind("click",cargar_calendario);
        $("#btn_guardar_calendario").bind("click",guardar_calendario);
    });

    function validar(){
        $.ajax({
            url: 'views/torneos/ajax/torneos_clasificados_validar.php',
            type: 'POST',
            dataType: 'json',
            data: {codigo_torneo: codigo_torneo},
            success: function(response){
               if(response.resultado==1){
                    $("#div_registro_torneo").show();
                    $("#btn_proxima_ronda").html("<h5>Proxima Ronda: "+response.proxima_ronda + "</h5><input type='hidden' id='numero_ronda' name='numero_ronda' value='"+response.proxima_ronda+"'>");

                    //SE MUESTRAN LOS EQUIPOS CLASIFICADOS
                    $("#div_equipos_clasificados").show();
                    if(response.equipos_clasificados.length>0){
                        var html="";
                        for(var i=0; i<response.equipos_clasificados.length; i++){
                            html+='<button type="button" class="btn btn-outline-success btn_equipo_clasificado">';

                            html+=" <input type='hidden' name='codigo_equipo[]' value='"+response.equipos_clasificados[i].codigo_equipo+"'>";
                            html+=" <input type='hidden' name='nombre_equipo[]' value='"+response.equipos_clasificados[i].nombre_equipo+"'>";
                            html+=" <input type='hidden' name='escudo[]' value='"+response.equipos_clasificados[i].escudo+"'>";

                            html+="<div style='width: 50px; height: 50px;'><img src='archivos/equipos/escudos/"+response.equipos_clasificados[i].escudo+"' style='width: 100%;object-fit: cover;'> </div>";
                            html+="<h6>"+response.equipos_clasificados[i].nombre_equipo+"</h6>";
                            html+="</button>";
                        }
                        $("#div_equipos_clasificados").html(html);

                        
                    }else{
                        $("#div_equipos_clasificados").html("<h5>No hay equipos clasificados</h5>");
                    }
                }else{
                    alert(response.mensaje);
                    window.location.href = "dashboard.php";
                }
            }
        });
    }

    function deplegableSedes(){
         
         $.ajax({
             url: "ajax/torneos/listado_sedes_torneo.php",
             type: "POST",
             async: false,   
             data:{
                 codigo_torneo: codigo_torneo
             },
             dataType: "json",
             success: function(datos) {
                sedes=datos;
             }
         });
    }

    
    function cargar_calendario(){
        if(validar_formulario("1","form_reg_calendario")){

            campos = $("#form_reg_calendario").serialize();
            campos+="&codigo_torneo="+codigo_torneo;

            $.ajax({
                type: 'POST',
                async: false,
                url: 'ajax/clasificacion/clasificacion_cargar_calendario.php',
                data : campos,
                success: function(datos){

                    $("#calendario").html("");

                  
                    if(datos.codigo_clasificacion==1){
                        //TODOS CONTRA TODOS
                        $("#calendario").html(formato_todos_contra_todos(datos));
                        
                    }else{
                        $("#calendario").html(formato_grupo(datos));
                    }

                    $("#btn_guardar_calendario").show();

                },
                dataType: 'json'
            });   
        }
    }

    function  guardar_calendario() {
        if(fechas_calendario==0){
            alert("Debe cargar las fechas del calendario");
            return false;
        }else{
            if(validar_formulario("1","form_reg_calendario")){

                ruta_guardar="";
                codigo_clasificacion=$("#codigo_clasificacion").val();

                //se recupera la ruta de guardado 
                rutas=consultar_campo("tbl_tafi_torneos_tipos_clasificacion","ajax_guardar,ajax_editar","codigo_clasificacion="+codigo_clasificacion);
                rutas=rutas.split(";");
                ruta_guardar=rutas[0];
                

                if(ruta_guardar!=""){
                    campos=$("#form_reg_calendario").serialize();
                    campos+="&codigo_torneo="+codigo_torneo;

                    $.ajax({
                        type: 'POST',
                        async: false,
                        url: ruta_guardar,
                        data : campos,
                        success: function(datos){
                            if(datos==""){
                            	alert("Calendario Registrado");
                            	window.location.href= "dashboard.php?proc=4";
                            }else{
                            	alert(datos);
                            }

                        },  
                        dataType: 'text'
                    });    
                }else{
                    alert("No se ha definido la ruta de guardado");
                }
                

            }  
        }
    }


</script>

<form id="form_reg_calendario" name="form_reg_calendario">

    <div class="card" style="padding:20px;" id="div_registro_torneo" style="display:none;">

    <button type="button" class="btn bg-gradient-success" id="btn_proxima_ronda"></button>

    <div id="equipos_clasificados" class="class_div">
        <h4>Equipos Clasificados</h4>
        <div id="div_equipos_clasificados"></div>
    </div>

    <div style="display: flex;align-items: center;" class="class_div">
        <div class="form-floating mb-1">

            <select class="form-control" name="codigo_clasificacion" id="codigo_clasificacion" type="text"  lang="1" style="width: 300px;">
                <option value="1">Todos Contra Todos</option>
                <option value="2">Fase de Grupos</option>
            </select>   
            <label for="name">Seleccione el Tipo de Clasificación</label>
        </div>

        <div class="form-floating mb-1">

            <select class="form-control" name="cantidad_rondas" id="cantidad_rondas" type="text"  lang="1" style="width: 300px;">
                <option value="1">Ida </option>
                <option value="2">Ida y Vuelta</option>
            </select>   
            <label for="name">Tipo de Partido</label>
        </div>

        <div class="form-group cont_inpt">
            <label for="example-number-input" class="form-control-label">Cantidad de Equipos que clasifican</label>
            <input class="form-control form_param imp_grupos" name="cantidad_equipos_clasifican" type="number"   id="example-number-input" lang="1" style="width: 300px">
        </div>
        
        <div class="form-group cont_inpt" id="div_cant_equipos" style="display:none;">
            <label for="example-number-input" class="form-control-label">Cantidad de Equipos por Grupos</label>
            <input class="form-control form_param imp_grupos" name="cantidad_equipos_grupos" type="number"  id="example-number-input" >
        </div>
    </div>

    <div>
        <button class="btn bg-gradient-info" id="btn_cargar_calendario" type="button"  aria-expanded="false">
            Consultar Calendario
        </button>
        <button class="btn bg-gradient-info" id="btn_guardar_calendario" style="display:none;" type="button"  aria-expanded="false">
            Guardar Calendario
        </button>
    </div>

    <div id="calendario" class="class_div"></div>

</div>
</form>


<style>
    .class_div{
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        margin-bottom: 10px;
    }

    #div_equipos_clasificados{
        display: flex;
        flex-wrap: wrap;
    }

    .btn_equipo_clasificado{
        margin:5px;
        width: 130px;
        height: 130px;
        display: flex;
        flex-direction: column;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    .form-floating{
        margin:5px;
    }

    .form-group{
        margin-left:5px;
    }

    #btn_proxima_ronda h5{
        color: #fff;
    }

    
</style>