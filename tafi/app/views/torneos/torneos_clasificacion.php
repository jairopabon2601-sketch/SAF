<script id="tmp_thead_clasificacion" type="text/template">
    <tr>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"></th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Equipo</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Puntos</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Partidos Jugados</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Victorias</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Empates</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Derrotas</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Goles a Favor</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Goles en Contra</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Diferencia de Goles</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tarjetas Amarillas</th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tarjetas Rojas</th>
        <th></th>
    </tr>   
</script>

<script id="tmp_tabla_prosiciones" type="text/template">

    <table class="table align-items-center mb-0" id="tbl_clasificacion_todos">
        <thead> {head} </thead>
        <tbody> {body} </tbody>
    </table>

</script>



<style>
    .img_tbl{
	    width: 40px;
    }

    .txt_icon{
        font-size: 20px;
        margin-right: 30px;
    }
    
    .div_info_ronda{
        width: 100%;display: grid;grid-template-columns: 50% 50%;
    }

    @media only screen and (max-width: 600px) {
       .div_info_ronda{
            margin-bottom: 2px;
            display: flex;
            grid-template-columns: 50% 50%;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            align-content: center;
       }
       
       .div_titulo_ronda{
            margin-bottom: 15px;
       }

       .titulo_ronda{
        font-size: 19px;
       }
    }
</style>

<link rel="stylesheet" href="views/torneos/css/acordeon.css">

<script>
    $(document).ready(function() {
        rellenar_select("tbl_tafi_torneos","codigo_torneo","nombre_torneo","codigo_responsable="+codigo_origen,"codigo_torneo","","nombre_torneo");  
        $("#codigo_torneo").bind("change",parametros_torneo);
    });

    function parametros_torneo(){
        datos=consultar_campo("tbl_tafi_torneos","codigo_clasificacion","codigo_torneo="+$("#codigo_torneo").val());
        datos=datos.split(";");

        tabla_goleadores();

        if(datos[0]==1){
            tabla_clasificacion();
            $("#div_clasificacion").show();
        }else{
            tabla_clasificacion_grupos();
        }
    }

    function tabla_clasificacion(){

        $.ajax({
            type: 'POST',
            async: false,
            data: {codigo_torneo: $("#codigo_torneo").val()},
            url: 'ajax/clasificacion/clasificacion_todo_con_todos.php',
            success: function(data){
                console.log(data);

                if(data.resultado==1){
                    html="";
                    x=1;
                    $(data.datos).each(function(i, v){ // indice, valor
                        html+="<tr>";
                        html+='<td style="text-align: center;">'+x+'</td>';

                        html+='<td><div class="d-flex px-2">';
                            html+='<div>';
                                html+='<img src="archivos/equipos/escudos/'+v.escudo+'" style="width: 43px;">';
                            html+='</div>';
                            
                            html+='<div class="my-auto">';
                                html+='<h6 class="mb-0 text-xs">'+v.nombre_equipo+'</h6>';
                            html+='</div>';
                        html+='</div></td>';

                        html+='<td>'+v.PTS+'</td>';
                        html+='<td>'+v.PJ+'</td>';
                        html+='<td>'+v.V+'</td>';
                        html+='<td>'+v.E+'</td>';
                        html+='<td>'+v.D+'</td>';
                        html+='<td>'+v.GF+'</td>';
                        html+='<td>'+v.GC+'</td>';
                        html+='<td>'+v.DIF+'</td>';
                        html+='<td>'+v.AMARILLAS+'</td>';
                        html+='<td>'+v.ROJAS+'</td>';   
                       
                        html+="</tr>";
                        x++;
                    });

                    header_tabla=$("#tmp_thead_clasificacion").text();
                    $("#tbl_clasificacion_todos thead").html(header_tabla);
                    $("#tbl_clasificacion_todos tbody").html(html);
                }else{
                    $("#tbl_clasificacion_todos tbody").html(data.mensaje);
                }
            },
            dataType: 'json'
        });

    }

    function tabla_clasificacion_grupos(){
        
        $.ajax({
            type: 'POST',
            async: false,
            data: {codigo_torneo: $("#codigo_torneo").val()},
            url: 'ajax/clasificacion/clasificacion_grupos.php',
            success: function(data){
                console.log(data);

                if(data.resultado==1){
                    html="";
                    ronda=0;
                    $(data.datos_rondas).each(function(){
                        num_clasficados=this.cantidad_equipos_clasifican;

                        

                        html+='<div class="accordion-item mb-3">';
                            html+='<h5 class="accordion-header" id="headingOne">';
                                html+='<button class="accordion-button border-bottom font-weight-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'+this.numero+'" aria-expanded="false" aria-controls="collapseOne">';

                                    html+='<div class="div_info_ronda">';
                                        html+='<div class="div_titulo_ronda"><span class="titulo_ronda"> Ronda '+this.numero +" "+this.nombre + '</span></div>';
                                        
                                        html+='<div style="display: flex;text-align: end;justify-content: flex-end;">'; 
                                            
                                            html+='<div><img src="iconos/calendario.png" class="img_tbl"> <span class="txt_icon">'+this.num_fechas+'</span></div>';
                                            html+='<div><img src="iconos/contra.png" class="img_tbl"> <span class="txt_icon">'+this.num_partidos+'</span></div>';
                                            html+='<div><img src="iconos/gol.png" class="img_tbl"> <span class="txt_icon">'+this.numero_goles+'</span></div>';
                                            html+='<div><img src="iconos/tarjeta-amarilla.png" class="img_tbl"> <span class="txt_icon">'+this.numero_amarillas+'</span></div>';
                                            html+='<div><img src="iconos/rojo.png" class="img_tbl"> <span class="txt_icon">'+this.numero_rojas+'</span></div>';
                                            
                                        html+='</div>';
                                    html+='</div>';

                                    html+='<i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>';
                                    html+='<i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>';
                                html+='</button>';
                            html+='</h5>';

                            html+='<div id="collapse'+this.numero+'" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental" style="">';
                                html+='<div class="accordion-body text-sm opacity-8">';


                                    if(this.codigo_clasificacion==1){

                                        html_posiciones="";
                                        x=1;
                                        $(data.datos_rondas[ronda].posiciones.datos).each(function(i, v){ 

                                            if(num_clasficados>=x){
                                                html_posiciones+="<tr style='background-color: #b0ffb3;'>";
                                            }else{
                                                html_posiciones+="<tr>";    
                                            }

                                            html_posiciones+='<td style="text-align: center;">'+x+'</td>';

                                            html_posiciones+='<td><div class="d-flex px-2">';
                                                html_posiciones+='<div>';
                                                    html_posiciones+='<img src="archivos/equipos/escudos/'+v.escudo+'" style="width: 43px;">';
                                                html_posiciones+='</div>';
                                                
                                                html_posiciones+='<div class="my-auto">';
                                                    html_posiciones+='<h6 class="mb-0 text-xs">'+v.nombre_equipo+'</h6>';
                                                html_posiciones+='</div>';
                                            html_posiciones+='</div></td>';

                                            html_posiciones+='<td>'+v.PTS+'</td>';
                                            html_posiciones+='<td>'+v.PJ+'</td>';
                                            html_posiciones+='<td>'+v.V+'</td>';
                                            html_posiciones+='<td>'+v.E+'</td>';
                                            html_posiciones+='<td>'+v.D+'</td>';
                                            html_posiciones+='<td>'+v.GF+'</td>';
                                            html_posiciones+='<td>'+v.GC+'</td>';
                                            html_posiciones+='<td>'+v.DIF+'</td>';
                                            html_posiciones+='<td>'+v.AMARILLAS+'</td>';
                                            html_posiciones+='<td>'+v.ROJAS+'</td>';   
                                        
                                            html_posiciones+="</tr>";
                                            x++;
                                        });

                                        tabla=$("#tmp_tabla_prosiciones").text();
                                        header_tabla=$("#tmp_thead_clasificacion").text();

                                        //SE REEMPLAZAN LOS DATOS
                                        tabla=tabla.replace("{head}",header_tabla);
                                        tabla=tabla.replace("{body}",html_posiciones);

                                        html+=tabla;

                                    }else{
                                        //CLASIFICACION POR GRUPOS
                                        $(data.datos_rondas[ronda].grupos).each(function(){
                                            html+='<span class="badge badge-pill badge-md bg-gradient-info">'+$(this)[0].grupo+'</span>';

                                            //SE CONSTRUYE LA TABLA DE POSICIONES
                                            html_posiciones="";
                                            x_grupos=1;
                                            $(this)[0].posiciones.datos.forEach(function(v){ 
                                                
                                                if(num_clasficados>=x_grupos){
                                                    html_posiciones+="<tr style='background-color: #b0ffb3;'>";
                                                }else{
                                                    html_posiciones+="<tr>";    
                                                }

                                                html_posiciones+='<td style="text-align: center;">'+x_grupos+'</td>';

                                                html_posiciones+='<td><div class="d-flex px-2">';
                                                    html_posiciones+='<div>';
                                                        html_posiciones+='<img src="archivos/equipos/escudos/'+v.escudo+'" style="width: 43px;">';
                                                    html_posiciones+='</div>';
                                                    
                                                    html_posiciones+='<div class="my-auto">';
                                                        html_posiciones+='<h6 class="mb-0 text-xs">'+v.nombre_equipo+'</h6>';
                                                    html_posiciones+='</div>';
                                                html_posiciones+='</div></td>';

                                                html_posiciones+='<td>'+v.PTS+'</td>';
                                                html_posiciones+='<td>'+v.PJ+'</td>';
                                                html_posiciones+='<td>'+v.V+'</td>';
                                                html_posiciones+='<td>'+v.E+'</td>';
                                                html_posiciones+='<td>'+v.D+'</td>';
                                                html_posiciones+='<td>'+v.GF+'</td>';
                                                html_posiciones+='<td>'+v.GC+'</td>';
                                                html_posiciones+='<td>'+v.DIF+'</td>';
                                                html_posiciones+='<td>'+v.AMARILLAS+'</td>';
                                                html_posiciones+='<td>'+v.ROJAS+'</td>';   
                                            
                                                html_posiciones+="</tr>";
                                                x_grupos++;
                                            });

                                            tabla=$("#tmp_tabla_prosiciones").text();
                                            header_tabla=$("#tmp_thead_clasificacion").text();
                                            
                                            //SE REEMPLAZAN LOS DATOS
                                            tabla=tabla.replace("{head}",header_tabla);
                                            tabla=tabla.replace("{body}",html_posiciones);

                                            html+=tabla;
                                        });
                                        
                                    }

                                html+='</div>';
                            html+='</div>';

                        html+="</div>";

                        ronda++;
                    });

                    $("#accordionRental").html(html);

                }else{
                    $("#div_clasificacion_grupos").html(data.mensaje);
                }

                $("#div_clasificacion_grupos").show();
            },
            dataType: 'json'
        });

    }

    function tabla_goleadores(){
        $.ajax({    
            type: 'POST',
            async: false,
            data: {codigo_torneo: $("#codigo_torneo").val()},
            url: 'views/torneos/ajax/clasificacion_listado_goleadores.php',
            success: function(data){
                console.log(data);

                if(data.length>0){

                    html="";
                    x=1;
                    $(data).each(function(i, v){ // indice, valor
                        html+="<tr>";
                            html+='<td style="text-align: center;">'+x+'</td>';

                            html+='<td>';
                                html+='<div class="d-flex px-2 py-1">';
                                html+='<div>';
                                html+='<img src="'+v.foto_jugador+'" class="avatar avatar-sm me-3">';
                                html+='</div>';
                                html+='<div class="d-flex flex-column justify-content-center">';
                                html+='<h6 class="mb-0 text-xs">'+v.jugador+'</h6>';
                                html+='<p class="text-xs text-secondary mb-0">'+v.numero_documento+'</p>';
                                html+=' </div>';
                                html+='</div>';
                            html+=' </td>';

                            html+='<td>';
                                html+='<div class="d-flex px-2 py-1">';
                                html+='<div>';
                                html+='<img src="'+v.escudo+'" class="avatar avatar-sm me-3">';
                                html+='</div>';
                                html+='<div class="d-flex flex-column justify-content-center">';
                                html+='<h6 class="mb-0 text-xs">'+v.nombre_equipo+'</h6>';
                                html+=' </div>';
                                html+='</div>';
                            html+=' </td>';

                            html+='<td>';
                                html+='<div class="d-flex px-2 py-1">';
                                html+='<div>';
                                html+='<h6 class="mb-0 text-xs">'+v.goles+'</h6>';
                                html+='</div>';
                                html+='</div>';

                            html+="</tr>";
                        x++;
                    });

                    $("#tbl_listado_goleadores tbody").html(html);
                }else{
                    $("#tbl_listado_goleadores tbody").html("No se encontraron datos");
                }
               
            },
            dataType: 'json'
        });
    }

</script>

<div class="card" style="padding:20px;" id="div_registro_torneo">

    <form id="form_calendario" name="form_calendario" enctype="multipart/form-data" action="" method="POST">

        <div style="display: flex;">
            <div class="form-floating mb-3">
                <select class="form-control" name="codigo_torneo" id="codigo_torneo" type="text"  lang="1" style="width: 300px;"></select>   
                <label for="name">Seleccione el Torneo</label>
            </div>

            <div class="form-floating mb-3" tyle="display: none;margin-left:5px;" id="div_ronda" style="display:none;">
                <select class="form-control" name="codigo_ronda" id="codigo_ronda" type="text"  lang="1" style="width: 300px;"></select>   
                <label for="name">Ronda</label>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ins-tab" data-bs-toggle="tab" data-bs-target="#li_clasificacion" type="button" role="tab" aria-controls="ins" aria-selected="true">
                    <img src="iconos/podio.png" class="iconos_tab">    
                    Clasificación
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#li_goleadores" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <img src="iconos/objetivo.png" class="iconos_tab">    
                    Goleadores
                </button>
            </li>
        </ul>    
        
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="li_clasificacion" role="tabpanel" aria-labelledby="ins-tab" >
                
                <div id="div_clasificacion" style="display:none;">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0" id="tbl_clasificacion_todos">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="div_clasificacion_grupos" style="display:none;">

                    <div class="accordion-1">
                        <div class="container">
                            
                            <div class="col-md-12 mx-auto">
                                <div class="accordion" id="accordionRental">

                                </div>
                                </div>
                        
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="tab-pane fade" id="li_goleadores" role="tabpanel" aria-labelledby="home-tab" >
                <div class="card">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="tbl_listado_goleadores" style="margin-top: 19px;">
                            <thead>
                                <tr>
                                    <th class="text-secondary opacity-7"></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jugador</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Equipo</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cantidad de Goles</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>    
        </div>
        

        

    </form>
</div>

<style>
    a:hover,
a:focus{
    text-decoration: none;
    outline: none;
}
.vertical-tab{
    font-family: 'Montserrat', sans-serif;
    display: table;
}
.vertical-tab .nav-tabs{
    width: 27%;
    min-width: 27%;
    border: none;
    vertical-align: top;
    display: table-cell;
}
.vertical-tab .nav-tabs li{ float: none; }
.vertical-tab .nav-tabs li a{
    color: #333;
    background: #f5f5f5;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 1px;
    text-align: center;
    text-transform: uppercase;
    padding: 11px 15px 10px;
    margin: 0 0 10px 0;
    border: none;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    border-radius: 0;
    overflow: hidden;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease 0s;
}
.vertical-tab .nav-tabs li a:hover,
.vertical-tab .nav-tabs li.active a,
.vertical-tab .nav-tabs li.active a:hover{
    color: #fff;
    background: #f5f5f5;
    border: none;
}
.vertical-tab .nav-tabs li a:before{
    content: '';
    background: #6f2aab;
    height: 100%;
    width: 100%;
    opacity: 0;
    transform: scale(0.5);
    position: absolute;
    left: 50%;
    top: 0;
    z-index: -1;
    transition: opacity 0.4s ease 0s,left 0.3s ease 0s,transform 0.4s ease 0.2s;
}
.vertical-tab .nav-tabs li.active a:before,
.vertical-tab .nav-tabs li a:hover:before{
    opacity: 1;
    transform: scale(1);
    left: 0;
}
.vertical-tab .tab-content{
    color: #888;
    background: linear-gradient(to top right,#f5f5f5 50%, transparent 50%);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    line-height: 23px;
    padding: 20px;
    border-bottom: 4px solid #6f2aab;
    border-left: 4px solid #6f2aab;
    display: table-cell;
}
.vertical-tab .tab-content h3{
    color: #6f2aab;
    font-size: 20px;
    font-weight: 700;
    text-transform: uppercase;
    margin: 0 0 7px;
}
@media only screen and (max-width: 479px){
    .vertical-tab .nav-tabs{
        width: 100%;
        margin: 0 0 10px;
        display: block;
    }
    .vertical-tab .nav-tabs li a{
        padding: 15px 10px 14px;
        margin-bottom: 10px;
    }
    .vertical-tab .tab-content{
        font-size: 14px;
        display: block; 
    }
}
</style>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
