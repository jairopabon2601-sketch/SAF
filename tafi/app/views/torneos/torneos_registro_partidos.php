
<script>
    let codigo_clasificacion=0;
    let tab=0;
  
    $(document).ready(function() {
       rellenar_select("tbl_tafi_torneos","codigo_torneo","nombre_torneo","codigo_responsable="+codigo_origen,"codigo_torneo","","nombre_torneo");  

       $("#codigo_torneo").bind("change",function(){
            cargar_rondas_torneo();
            $("#div_ronda").show(); 
            //se actitiva trigger de ronda
            $("#codigo_ronda").trigger("change");
       });

       $("#codigo_fecha").bind("change",function(){
            if(codigo_clasificacion==2 && tab==0){
                $(".tabs_grupos").first().click();
                tab=$(".tabs_grupos").first().attr("rel");
                cargar_partidos();
            }else{
                cargar_partidos();
            }

            $("#listado_partidos").show();
       });
        
       $("#codigo_ronda").bind("change",function(){
            cargar_datos_fecha();
            $("#listado_partidos").hide();
       });
       
       $(".tabs_grupos").bind("click",function(){
            tab=$(this).attr("rel");
            $("#codigo_grupo").val(tab);
            cargar_partidos();
        });
    });

    function cargar_datos_fecha(){
        const codigo_torneo=$("#codigo_torneo").val();
        filtro="codigo_torneo='"+codigo_torneo+"'";
        filtro+=" and codigo_ronda='"+$("#codigo_ronda").val()+"' ";

        rellenar_select("tbl_tafi_torneos_calendario_fechas","codigo_fecha","nombre_fecha",filtro,"codigo_fecha","","nombre_fecha"); 

        datos_ronda=consultar_campo("tbl_tafi_torneos_calendario_fechas_rondas","codigo_clasificacion","codigo_torneo="+codigo_torneo +" and codigo_ronda="+$("#codigo_ronda").val());
        codigo_clasificacion=datos_ronda;

        if(codigo_clasificacion==2){
            cargar_grupos_torneo();
        }
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

    function cargar_grupos_torneo(){

        $.ajax({
            type: 'POST',
            async: false,
            url: 'ajax/torneos/calendario_cargar_grupos_torneo.php',
            data: {
                codigo_torneo: $("#codigo_torneo").val()
            },
            success: function(data){
               if(data.length>0){
                    //SE CREAN TABS POR GRUPOS
                    tabs=' <ul class="nav nav-tabs" id="myTab" role="tablist"> {{head_tabs}} </ul>';
                    tabs+='<div class="tab-content" id="myTabContent"> {{body_tabs}} </div>';

                    head="";
                    body="";
                    $(data).each(function(i, v){ 
                        head+='<li class="nav-item" role="presentation">';
                            head+='<button class="nav-link tabs_grupos" rel="'+v.codigo_grupo+'" id="'+v.codigo_grupo+'-tab" data-bs-toggle="tab" data-bs-target="#tab-'+v.codigo_grupo+'" type="button" role="tab" aria-controls="ins" aria-selected="true">';
                                head+='GRUPO '+v.nombre_grupo;
                            head+='</button>';
                        head+='</li>';
                        body+='<div class="tab-pane fade show active" id="tab-'+v.codigo_grupo+'" role="tabpanel" aria-labelledby="'+v.codigo_grupo+'-tab" >';
                        body+='</div>';
                    });

                    tabs=tabs.replace("{{head_tabs}}",head);
                    tabs=tabs.replace("{{body_tabs}}",body);

                    $("#listado_partidos").html(tabs);

               }
            },
            dataType: 'json'
        });
    }

    function cargar_partidos(){

        campos = $("#form_calendario").serialize();
        campos+="&codigo_clasificacion="+codigo_clasificacion;

        $.ajax({
            type: 'POST',
            async: false,
            url: 'ajax/torneos/calendario_listado_partidos.php',
            data: campos,
            success: function(data){

                console.log(data);

                if(data.length>0){
                    //partidos
                    html="<h6>Listado de Partidos</h6>";
                    html+='<div class="sortable-list list_partidos">';
                    $.each(data, function(i, item) {
                        html+='<a htef="#" class="a_fecha" codigo_calendario="'+item.codigo_calendario+'" key="'+item.llave+'" permite_registro="'+item.permite_registro+'">';
                            html+='<div id="div_cont_fechas"';
                            
                            if(item.permite_registro==0){
                                html+=' class="div_no_disponible" ';
                            }

                            html+='><div class="item">';
                                    html+='<div class="details">';
                                        html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item.escudo_local+'" alt="Team Logo"></div>';
                                        html+='<span>'+item.equipo_local+' <b>('+item.resultado_local+')</b> </span>';

                                    html+='</div>';

                                    html+=' VS';
                                    
                                    html+='<div class="details">';
                                        
                                        html+='<span><b>('+item.resultado_visitante+')</b> '+item.equipo_visitante+'</span> ';
                                        html+=' <div  class="div_con_img"><img src="archivos/equipos/escudos/'+item.escudo_visitante+'" alt="Team Logo"></div>';
                
                                    html+='</div>';
                                
                                html+='</div>';

                                html+='<div style="display: flex;">';
                                    html+='<input type="date" value="'+item.fecha+'" readonly class="form-control">';
                                    html+='<input type="time" value="'+item.hora+'" readonly class="form-control">';
                                html+='</div>';

                                html+='<div style="display: flex;">';
                                    html+='<h6>Lugar: '+item.sede +'</h6><br>';
                                    
                                html+='</div>';

                                html+='<div style="display: flex;">';
                                    html+='<h6>Estado del Partido: '+item.estado +'</h6>';
                                html+='</div>';
                              
                            html+='</div>';
                            
                        html+='<a>';

                        
                    });
                    html+='</div>';

                    if(codigo_clasificacion==2){
                        $("#tab-"+tab).html(html);
                    }else{
                        $("#listado_partidos").html(html);
                    }

                    $(".a_fecha").bind("click",function(){
                        if($(this).attr("permite_registro")==0){
                            alert("No se puede registrar el partido, ya que no se ha definido la fecha, hora y/o sede");
                        }else{
                            window.location.href="dashboard.php?proc=16&codigo_calendario="+$(this).attr("codigo_calendario")+"&key="+$(this).attr("key");                        }
                    });
                }else{
                    $("#listado_partidos").html("<h3>No se encontraron partidos para el filtro seleccionado</h3>");
                }
            },
            dataType: 'json'
        });
    }
</script>

<div class="card" style="padding:20px;" id="div_registro_torneo">

    <form id="form_calendario" name="form_calendario" enctype="multipart/form-data" action="" method="POST">
        <input type="hidden" name="codigo_grupo" id="codigo_grupo">    

        <div id="container-filtros" class="container-filtros">
            <div class="form-floating mb-3">
                <select class="form-control" name="codigo_torneo" id="codigo_torneo" type="text"  lang="1" style="width: 300px;"></select>   
                <label for="name">Seleccione el Torneo </label>
            </div>

            <div class="form-floating mb-3" id="div_ronda" style="display:none;margin-left:5px;">
                <select class="form-control" name="codigo_ronda" id="codigo_ronda" type="text"  style="width: 300px;"></select>   
                <label for="name">Ronda</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-control" name="codigo_fecha" id="codigo_fecha" type="text"  lang="1" style="width: 300px;"></select>   
                <label for="name">Seleccione la Fecha</label>
            </div>
            
        </div>

        <div id="listado_partidos">
        
        </div>

    </form>
</div>

<style>
    .div_con_img{
        width: 50px;
    }

    .div_con_img img{
        width: 100%;

    }


    .div_no_disponible {
        -webkit-filter: grayscale(1);
        background: antiquewhite;
    }

    .div_no_disponible .item {
        background: antiquewhite !important;
    }

    .div_no_disponible input {
        background: antiquewhite !important;
    }

    .form-floating{
        margin-right: 7px;
    }

    .container-filtros{
        display: flex;
        flex-wrap: wrap;
        align-content: center;
        justify-content: flex-start;
        align-items: center;
    }
    .container-grupos{
        display: grid;
        grid-template-columns: 33% 33% 33%;
    } 

   .container-todos-todos{
        display: grid;
        grid-template-columns:50% 50%;
    }

    .sortable-list .item {
        list-style: none;
        display: flex;
        background: #fff;
        align-items: center;
        border-radius: 5px;
        padding: 10px 13px;
        margin-bottom: 11px;
        /* box-shadow: 0 2px 4px rgba(0,0,0,0.06); */
        border: 1px solid #ccc;
        justify-content: space-between;
    }

    .item .details {
        display: flex;
        align-items: center;
    }

    .item .details img {
        wigth: 100%;
        pointer-events: none;
        margin-right: 12px;
        object-fit: cover;
    }

    .item .details span {
        font-size: 1.13rem;
    }

    .item i {
        color: #474747;
        font-size: 1.13rem;
    }

    .item.dragging {
        opacity: 0.6;
    }
    
    .item.dragging :where(.details, i) {
        opacity: 0;
    }

    #div_cont_fechas{
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-bottom: 7px;
        padding: 7px;
        width: 400px;
        margin: 5px;
    }

    #div_cont_fechas .item{
        border: none;
        margin-bottom: 0px;
    }

    #div_fechas{
        width: 400px;
    }

    .div_fechas_grupos{
        width: 100%;
    }

    #div_fechas img {
        border-radius : 0px !important;
        object-fit: contain;
    }


    #div_cont_fechas .form-control{
        margin: 5px;
        width: 50%;
    }

    .list_partidos{
        display: flex !important;
        flex-wrap: wrap  !important;
    }
</style>