<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="views/torneos/js/formato_grupo.js?v=20243101"></script>
<script src="views/torneos/js/formato_todos_contra_todos.js?v=2323"></script>


<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="views/torneos/css/registro_calendario.css?v=5221">

<script>
    let codigo_clasificacion=0;
    let calendario_registrado=0;
    let fechas_calendario=0;
    let sedes="";
   
    $(document).ready(function() {
       
       rellenar_select("tbl_tafi_torneos","codigo_torneo","nombre_torneo","codigo_responsable="+codigo_origen,"codigo_torneo","","nombre_torneo");  

       $("#codigo_torneo").bind("change",parametros_torneo);
       $("#btn_cargar_calendario").bind("click",validar_inputs);
       $("#btn_guardar_calendario").bind("click",guardar_calendario);
       $("#div_pendiente_ronda").bind("click",registro_calendario_clasificados);
       $("#codigo_ronda").bind("change",cargar_calendario);

       $("#div_finalizacion_torneo").bind("click",function(){
            window.location.href= "dashboard.php?proc=17";
       });
    });

    function registro_calendario_clasificados(){
        codigo_torneo=$("#codigo_torneo").val();
        codigo_ronda=$("#codigo_ronda").val();

        if(codigo_torneo!=0 && codigo_ronda!=0){
            window.location.href= "dashboard.php?proc=18&codigo_torneo="+codigo_torneo+"&codigo_ronda="+codigo_ronda;
        }
    }

    function parametros_torneo(){
        if($("#codigo_torneo").val()!=0){
            $("#div_parametros_torneo").show();
            datos=consultar_campo("tbl_tafi_torneos","codigo_clasificacion, calendario_registrado","codigo_torneo="+$("#codigo_torneo").val());

            datos=datos.split(";");
            codigo_clasificacion=datos[0];
            calendario_registrado=datos[1];
            sedes=deplegableSedes();

            if(calendario_registrado==1){

                if(codigo_clasificacion==2){
                    $("#div_ronda").show();
                    cargar_rondas_torneo();
                }
                
            	cargar_calendario();
                fechas_calendario=1;

            	$("#btn_guardar_calendario").html("Editar Calendario");
            	$("#btn_cargar_calendario").remove();
            	$("#div_cont_parametros").remove();

            }

            if(codigo_clasificacion==1){
                //TODOS CONTRA TODOS
                $("#datos_todos_contra_todos").show();
                $("#datos_fase_grupo").hide();

                $(".imp_todos_todos").attr("lang","1");
                $(".imp_grupos").attr("lang","0");
            }else{
                //FASE DE GRUPOS
                $("#datos_todos_contra_todos").hide();
                $("#datos_fase_grupo").show(); 
                
                $(".imp_grupos").attr("lang","1");
                $(".imp_todos_todos").attr("lang","0");
            }
        }else{
            $("#div_parametros_torneo").hide();
            alert("Debe seleccionar un torneo");
        }

        cargar_items_clasificacion();
    }

    function cargar_rondas_torneo(){

        $.ajax({
            type: 'POST',
            async: false,
            data:{
                codigo_torneo: $("#codigo_torneo").val()
            },
            url: 'views/torneos/ajax/torneos_cargar_rondas_clasificacion.php',
            success: function(data){
                if(data.resultado==1){

                    html="";
                    $(data.datos).each(function(){
                        html+="<option value='"+$(this)[0].codigo_ronda+"'>Ronda " +$(this)[0].numero+ "</option>";
                    });

                    $("#codigo_ronda").html(html);
                }
                
            },
            dataType: 'json'
        });

    }

    function validar_inputs() {
        if(codigo_clasificacion==2 && $("[name='cantidad_equipos_grupos']").val()=="" ){
            alert("Debe ingresar la cantidad de equipos por grupo");
            $(".accordion-button").eq(0).click();
            $("[name='cantidad_equipos_grupos']").focus();
            return false;
        }else{
            cargar_calendario();
        }
    }

    function cargar_items_clasificacion(){

        $.ajax({
            type: 'POST',
            async: false,
            url: 'ajax/torneos/torneos_item_clasificacion.php',
            success: function(data){
                if(data.length>0){

                    $("#items_clasificacion").html("");

                    html='<ul id="sortable">';
                        $.each(data, function(i, item) {
                            html+='<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';
                            html+='<input type="hidden"  name="codigo_item_clasificacion[]" value="'+item.codigo_item_clasificacion+'">'+item.item+'</li>';
                        });
                    html+='</ul>';

                    $("#items_clasificacion").html(html);  
                    $("#sortable").sortable({
                        handle: ".ui-icon"
                    });
                    $("#sortable").disableSelection();

                }else{
                    $("#items_clasificacion").html("<p>Este torneo no requiere item de clasificacion</p>");
                }
            },
            dataType: 'json'
        });

    }

    function deplegableSedes(){
         
        $.ajax({
            url: "ajax/torneos/listado_sedes_torneo.php",
            type: "POST",
            async: false,   
            data:{
                codigo_torneo: $("#codigo_torneo").val()
            },
            dataType: "json",
            success: function(datos) {
                datos_desplegable=datos;
            }
        });

        return datos_desplegable;
    }

    function cargar_calendario(){

        campos=$("#form_calendario").serialize();

        $.ajax({
            type: 'POST',
            async: false,
            url: 'ajax/torneos/calendario_cargar.php',
            data : campos,
            success: function(datos){

                $("#calendario").html("");
                codigo_clasificacion=datos.codigo_clasificacion;

                if(datos.calendario_registrado==0){

                    if(datos.codigo_clasificacion==1){
                        //TODOS CONTRA TODOS
                        $("#calendario").html(formato_todos_contra_todos(datos));
                        
                    }else{
                        $("#calendario").html(formato_grupo(datos));
                    }

                }else{

                    $("#calendario").html(cargarCalendarioRegistrado(datos));
                }

            },
            dataType: 'json'
        });

    }

    function cargarCalendarioRegistrado(datos){

        html="";

        if(datos.codigo_clasificacion==1){
            html="<br><h6>Partidos Todos Contra Todos</h6>";

                    html+='<div class="sortable-list list_partidos">';
                        fechas=[];
                        fecha_actual="";
                        $.each(datos.partidos, function(i2, item2) {
                            if(fecha_actual!=item2.nombre_fecha){
                                fecha_actual=item2.nombre_fecha;
                                fechas.push(fecha_actual);
                            }
                        });

                        $.each(fechas,function(i,item_fecha){
                            html+="<div id='div_fechas'>";

                                html+="<h6> "+item_fecha+"</h6>";

                                
                                filter={
                                    nombre_fecha: item_fecha,
                                }

                                partidos=filtrarCalendario(datos.partidos,filter);

                                $.each(partidos, function(ip, partido) {
                                    html+='<div id="div_cont_fechas"><div class="item">';
                                                html+='<input type="hidden" name="codigo_calendario[]" value="'+partido.codigo_calendario+'">';

                                                html+='<div class="details">';
                                                    html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+partido.esq_eq_local+'" alt="Team Logo"></div>';
                                                    html+='<span>'+partido.nom_eq_local+'</span>';
                                                html+='</div>';

                                                html+=' VS';
                                                
                                                html+='<div class="details">';   
                                                    html+='<span>'+partido.nom_eq_visitante+'</span> ';
                                                    html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+partido.esq_eq_visitante+'" alt="Team Logo"></div>';
                                                html+='</div>';

                                        html+='</div>';

                                        readonly = partido.permite_modificacion==0 ? "readonly" : "";

                                        html+='<div style="display: flex;">';
                                            html+='<input type="date" name="fecha_'+partido.codigo_calendario+'" id="" value="'+partido.fecha+'" class="form-control" '+readonly+'>';
                                            html+='<input type="time" name="hora_'+partido.codigo_calendario+'" id="" value="'+partido.hora+'" class="form-control" '+readonly+'>';
                                        html+='</div>';

                                        html+='<div class="form-floating mb-3">';
                                                html+='<select class="form-control" name="sede_'+partido.codigo_calendario+'"  style="width: 95%;" '+readonly+' >';
                                                    html+='<option value="0">Seleccione la Sede</option>';
                                                    $.each(sedes, function(i, item_sedes) {
                                                        
                                                        if(partido.codigo_sede>0 && item_sedes.codigo_sede==partido.codigo_sede){
                                                            html+='<option value="'+partido.codigo_sede+'" selected>'+item_sedes.nombre+'</option>';
                                                        }else{
                                                            html+='<option value="'+item_sedes.codigo_sede+'">'+item_sedes.nombre+'</option>';
                                                        }
                                                    
                                                    });
                                                html+='</select>';   
                                                html+='<label for="name">Seleccione la Sede </label>';
                                                
                                        html+='</div>';

                                        html+='<div class="form-floating mb-3">';
                                                html+='<button class="btn bg-gradient-info" type="button" onclick="imprimir_flayer('+partido.codigo_calendario+')">Imprimir Flayer</button>';
                                        html+='</div>';

                                    html+='</div>';
                                });
                            html+='</div>';
                        });

                    html+='</div>';
        }else{


            if(datos.partidos_restantes==0){

                if(datos.cantidad_equipos_clasifican==1){
                    $("#div_finalizacion_torneo").show();
                    $("#btn_guardar_calendario").hide();
                    $("#div_pendiente_ronda").hide();
                }else{
                    $("#div_pendiente_ronda").show();
                    $("#btn_guardar_calendario").hide();
                }

            }else{
                $("#div_pendiente_ronda").hide();
                $("#btn_guardar_calendario").show();
            }

            html="<br><h6>Partidos por Grupo</h6>";
            
            $.each(datos.grupos_letras, function(i, item) {
				
                html+='<div class="sortable-list list_partidos">';
                    html+='<div class="div_fechas_grupos">';
                        
                        
                        html+="<div class='div_fase_grupos'>";

                            html+="<div>";
                                html+='<h6>Grupo '+item.nombre_grupo+'</h6>';

                                filter={
                                	codigo_grupo: item.codigo_grupo
                                }

                                equipos_grupos=filtrarCalendario(datos.grupos,filter);

                                 $.each(equipos_grupos, function(i_e, item_equipo) {
                                 		html+='<div id="div_cont_fechas"><div class="item">';
                                                html+='<div class="details">';
                                                    html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item_equipo.escudo+'" alt="Team Logo"></div>';
                                                    html+='<span>'+item_equipo.nombre_equipo+'</span>';
                                                html+='</div>';
                                        html+='</div></div>';
                                 });

                            html+='</div>';
                            
                            html+="<div style='margin-left: 20px;'>";
                                html+='<h6>Partidos</h6>';


                                filter={
                                	codigo_grupo: item.codigo_grupo,
                                }

                                fechas=filtrarCalendario(datos.partidos,filter);

                                nombre_fecha="";
                                eq=1;
                                $.each(fechas, function(i_e, item_equipo) {
                                		
                                		if(nombre_fecha!=item_equipo.nombre_fecha){
                                			nombre_fecha=item_equipo.nombre_fecha;
                                			html+="<h6> "+nombre_fecha+"</h6>";     	
                                		}
                                		
                                        html+='<div id="div_cont_fechas"><div class="item">';
                                            html+='<input type="hidden" name="codigo_calendario[]" value="'+item_equipo.codigo_calendario+'">';

                                            html+='<div class="details">';
                                                html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item_equipo.esq_eq_local+'" alt="Team Logo"></div>';
                                                html+='<span>'+item_equipo.nom_eq_local+'</span>';
                                            html+='</div>';
                    
                                                html+=' VS';
                                                
                                                html+='<div class="details">';
                                                    html+='<span>'+item_equipo.nom_eq_visitante+'</span> ';
                                                    html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item_equipo.esq_eq_visitante+'" alt="Team Logo"></div>';
                                                html+='</div>';
                                            
                                            html+='</div>';

                                            html+='<div style="display: flex;">';
                                                if(item_equipo.codigo_calendario!=null){
                                                    html+='<input type="date" name="fecha_'+item_equipo.codigo_calendario+'" id="" value="'+item_equipo.fecha+'" class="form-control">';
                                                }else{
                                                    html+='<input type="date" name="fecha_'+item_equipo.codigo_calendario+'" id=""  class="form-control">';
                                                }
                                                 
                                                if(item_equipo.codigo_calendario!=null){
                                                    html+='<input type="time" name="hora_'+item_equipo.codigo_calendario+'" id="" value="'+item_equipo.hora+'" class="form-control">';
                                                }else{
                                                    html+='<input type="time" name="hora_'+item_equipo.codigo_calendario+'" id=""  class="form-control">';
                                                }
                                                
                                            html+='</div>';
                                            
                                            html+='<div class="form-floating mb-3">';
                                                html+='<select class="form-control" name="codigo_sede[]" id="codigo_sede" type="text"  style="width: 100%;">';
                                                    html+='<option value="0">Seleccione la Sede</option>';
                                                    $.each(sedes, function(i, item_sedes) {
                                                        if(item_equipo.codigo_sede>0 && item_sedes.codigo_sede==item_equipo.codigo_sede){
                                                            html+='<option value="'+item_sedes.codigo_sede+'" selected>'+item_sedes.nombre+'</option>';
                                                        }else{
                                                            html+='<option value="'+item_sedes.codigo_sede+'">'+item_sedes.nombre+'</option>';
                                                        }
                                                    
                                                    });
                                                html+='</select>';   
                                                html+='<label for="name">Seleccione la Sede </label>';
                                            html+='</div>';


                                            //boton imprimir flayer

                                            html+='<div class="form-floating mb-3">';
                                                html+='<button class="btn bg-gradient-info" type="button" onclick="imprimir_flayer('+item_equipo.codigo_calendario+')">Imprimir Flayer</button>';
                                            html+='</div>';

                                        html+='</div>';
                                    eq++;
                                });
                               
                                 
                            html+='</div>';
                        html+='</div>';

                    html+='</div>';
                html+='</div><br>';
            
            });

        }

        return html;
    }

    function  imprimir_flayer(codigo_calendario) {
        // window open target blank
        window.open("dashboard.php?proc=20&codigo_calendario="+codigo_calendario, '_blank');
    }

    function filtrarCalendario(data,filter){
    	return resultado= data.filter(function(item) {
            for (var key in filter) {
                if (item[key] === undefined || item[key] != filter[key])
                return false;
            }
            return true;
        });
    }

   
    function  guardar_calendario() {
        if(fechas_calendario==0){
            alert("Debe cargar las fechas del calendario");
            return false;
        }else{
            if(validar_formulario("1","form_calendario")){

                ruta_guardar="";
                //se recupera la ruta de guardado 
                rutas=consultar_campo("tbl_tafi_torneos_tipos_clasificacion","ajax_guardar,ajax_editar","codigo_clasificacion="+codigo_clasificacion);
                rutas=rutas.split(";");

                if(calendario_registrado==1){
                    ruta_guardar=rutas[1];
                }else{
                    ruta_guardar=rutas[0];
                }

                if(ruta_guardar!=""){
                    campos=$("#form_calendario").serialize();

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
                

            }else{
                $(".accordion-button").eq(0).click();
            }     
        }
    }
    
</script>

<div class="card" style="padding:20px;" id="div_registro_torneo">

    <form id="form_calendario" name="form_calendario" enctype="multipart/form-data" action="" method="POST">

        <div style="display:flex;" class="card">
            <div class="form-floating mb-3">
                <select class="form-control" name="codigo_torneo" id="codigo_torneo" type="text"  lang="1" style="width: 300px;">

                </select>   
                <label for="name">Seleccione el Torneo </label>
            </div>

            <div class="form-floating mb-3" id="div_ronda" style="display:none;margin-left:5px;">
                <select class="form-control" name="codigo_ronda" id="codigo_ronda" type="text"  style="width: 300px;"></select>   
                <label for="name">Ronda</label>
            </div>
        </div>

        <div id="div_finalizacion_torneo" class="alert alert-success" role="alert" style="display: none; cursor: pointer;color: black;">
            <strong>Se ha finalizado el torneo, haga click aqui para consultar la clasificación. </strong>
        </div>

        <div id="div_pendiente_ronda" class="alert alert-warning" role="alert" style="display: none; cursor: pointer;color: black;">
            <strong>Todos los partidos se le han registrado resultados, haga click aqui para cargar el calendario de los equipos clasificados. </strong>
        </div>

        <div id="div_parametros_torneo" style="display:none;">
            <div class="accordion-1" id="div_cont_parametros">
                <div class="container">
                
                    <div class="col-md-12 mx-auto">
                        <div class="accordion" id="accordionRental">
                            
                        <div class="accordion-item mb-3">
                            <h5 class="accordion-header" id="headingOne">
                            <button class="accordion-button border-bottom font-weight-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Parametros del Torneo
                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                            </button>
                            </h5>

                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental" style="">
                                <div class="accordion-body text-sm opacity-8">

                                    <div id="datos_parametros">

                                        <div class="form-floating cont_inpt">
                                            <select class="form-control form_param" name="fixture" id="fixture" type="text" lang="1" >
                                                <option value="1">Automatico</option>
                                                <!--<option value="2">Manual</option>-->
                                            </select>   
                                            <label for="name">FIXTURE</label>
                                        </div>


                                        <div id="datos_todos_contra_todos" style="display:none;">
                                            <div class="form-group cont_inpt">
                                                <label for="example-number-input" class="form-control-label">Cantidad de Equipos que clasifican</label>
                                                <input class="form-control form_param imp_todos_todos" name="cantidad_equipos" type="number" id="example-number-input" lang="1">
                                            </div>
                                        </div>

                                        <div id="datos_fase_grupo" style="display:none;    display: flex;">
                                            <div class="form-group cont_inpt">
                                                <label for="example-number-input" class="form-control-label">Cantidad de Equipos por Grupos</label>
                                                <input class="form-control form_param imp_grupos" name="cantidad_equipos_grupos" type="number  id="example-number-input" lang="1">
                                            </div>

                                            <div class="form-group cont_inpt">
                                                <label for="example-number-input" class="form-control-label">Cantidad de Equipos que clasifican Por grupo</label>
                                                <input class="form-control form_param imp_grupos" name="cantidad_equipos_clasifican" type="number"   id="example-number-input" lang="1">
                                            </div>
                                        </div>                
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3">
                            <h5 class="accordion-header" id="headingTwo">
                            <button class="accordion-button border-bottom font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Item de Clasificacion
                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                            </button>
                            </h5>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionRental">
                                <div class="accordion-body text-sm opacity-8">
                                    <div id="items_clasificacion"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
               
                    </div>
                </div>
            </div>

            <button class="btn bg-gradient-info" id="btn_cargar_calendario" type="button"  aria-expanded="false">
                Cargar Fechas
            </button>
            <button class="btn bg-gradient-info" id="btn_guardar_calendario" type="button"  aria-expanded="false">
                Guardar Calendario
            </button>
            
        </div>

        <div id="calendario"></div>

    </form>
</div>
