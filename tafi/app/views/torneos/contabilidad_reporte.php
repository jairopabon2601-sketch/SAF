
<script>
    
    var porcentaje_torneo=0;
    var porcentaje_tafi=0;
    var porcentaje_interno_torneo=0;
    var costo_arbitraje=0;

    $(document).ready(function() {
       rellenar_select("tbl_tafi_torneos","codigo_torneo","nombre_torneo","codigo_responsable="+codigo_origen,"codigo_torneo","","nombre_torneo");  

        $("#codigo_torneo").bind("change",function(){
            cargar_inscripciones();
            cargar_contabilidad();
            cargar_rondas_torneo();
            s();
            $("#div_reporte").show(); 
        });

       $("#codigo_ronda").bind("change",s);
       $("#btn_grabar_registro_abono").bind("click",grabar_registro_abono);
    });

    function grabar_registro_abono(){
        if(validar_formulario("1","form_registro_abono")){
            campos=$("#form_registro_abono").serialize();
            campos+="&codigo_torneo="+$("#codigo_torneo").val();

            $.ajax({
                url: "views/torneos/ajax/registro_abono_arbitraje.php",
                type: "POST",
                data: campos,
                dataType: "text",
                success: function(respuesta) {
                    alert(respuesta);
                    $("#form_registro_abono")[0].reset();
                    $("#btn_registro_abono").click();
                    s();
                }
            });
        }
    }

    function cargar_inscripciones(){
        $.ajax({
            type: 'POST',
            async: false,
            data:{
                codigo_torneo: $("#codigo_torneo").val()
            },
            url: 'views/torneos/ajax/contabilidad_cargar_inscripciones.php',
            success: function(data){
                if(data.resultado==1){
                    html="";
                    $(data.datos_inscipcion).each(function(){
                        html+="<tr>";
                            html+="<td><h6 class='mb-0 text-xs'>"+$(this)[0].nombre_equipo+"</h6>";
                            html+="<td><h6 class='mb-0 text-xs'>"+$(this)[0].pagado+"</h6>";
                            html+="<td><h6 class='mb-0 text-xs'>"+$(this)[0].saldo+"</h6>";
                        html+="</tr>";
                    });

                    $("#tbl_inscripcion tbody").html(html);

                }else{
                    $("#tbl_inscripcion tbody").html("No se encontraron registros");
                }
                
            },
            dataType: 'json'
        });
    }

    function cargar_contabilidad(){

        $.ajax({
            type: 'POST',
            async: false,
            data:{
                codigo_torneo: $("#codigo_torneo").val()
            },
            url: 'views/torneos/ajax/contabilidad_cargar_reporte.php',
            success: function(data){
                
                porcentaje_torneo=data.datos_porcentajes[0].porcentaje_torneo;
                porcentaje_tafi=data.datos_porcentajes[0].porcentaje_tafi;
                porcentaje_interno_torneo=data.datos_porcentajes[0].porcentaje_interno_torneo;

                $("#porcentaje_torneo").html(porcentaje_torneo+'% <div class="progress"><div class="progress-bar bg-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: '+porcentaje_torneo+'%;"></div></div>');
                $("#porcentaje_tafi").html(porcentaje_tafi+'% <div class="progress"><div class="progress-bar bg-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: '+porcentaje_tafi+'%;"></div></div>');

                html="";
                $(data.datos_porcentajes).each(function(){
                    html+="<tr>";
                        html+="<td><h6 class='mb-0 text-xs'>"+$(this)[0].conceptos+"</h6>";
                        html+="<td><h6 class='mb-0 text-xs'>"+$(this)[0].costo+"</h6>";
                        html+="<td><h6 class='mb-0 text-xs'>"+$(this)[0].total_porcentaje_torneo+"</h6>";
                        html+="<td><h6 class='mb-0 text-xs'>"+$(this)[0].total_porcentaje_tafi+"</h6>";
                    html+="</tr>";

                    if($(this)[0].codigo_conceptos==2){
                        costo_arbitraje=$(this)[0].costo;
                    }
                });         
                
                $("#tbl_porcentajes tbody").html(html);
            },
            dataType: 'json'
        });
    }

    function s(){
        $("#div_cargando").show();
        $("#reporte_resultado").hide();

        $.ajax({
            type: 'POST',
            async: true,
            data:{
                codigo_ronda: $("#codigo_ronda").val()
            },
            url: 'views/torneos/ajax/contabilidad_cargar_reporte_resultado.php',
            success: function(data){

                $("#div_cargando").hide();
                $("#reporte_resultado").show();
                $(".porcentaje_arbitro").html(porcentaje_interno_torneo+ "%");

                datos_contabilidad_amarillas=data.datos_contabilidad_amarillas;
                datos_contabilidad_rojas=data.datos_contabilidad_rojas;
                datos_contabilidad_arbitraje=data.datos_contabilidad_arbitraje;

                porc_a_torneo= (costo_arbitraje * porcentaje_torneo / 100) - (((costo_arbitraje * porcentaje_torneo / 100) * porcentaje_interno_torneo ) /100);
                porc_a= (((costo_arbitraje * porcentaje_torneo / 100) * porcentaje_interno_torneo ) /100);
                porc_a_tafi=((costo_arbitraje * porcentaje_tafi) / 100);

                console.log(datos_contabilidad_arbitraje);

                //LISTADO DE EQUIPO 
                html="";
                html_arbi="";
                html_a="";
                html_r="";
                e=1;
                $(data.datos_equipos).each(function(){
                    
                    
                    html+="<tr>";
                        html+="<td>" + e + "</td>";
                        html+="<td>" + $(this)[0].elocal + "</td>";
                        html+="<td> VS </td>";
                        html+="<td>" + $(this)[0].evisitante + "</td>";
                        html+="<td>" + $(this)[0].fecha + "</td>";
                    html+="</tr>";

                    //tabla de amarillas 
                    filter_l= {
                        codigo_calendario: $(this)[0].codigo_calendario, 
                        codigo_equipo:  $(this)[0].codlocal
                    }

                    filter_v= {
                        codigo_calendario: $(this)[0].codigo_calendario, 
                        codigo_equipo:  $(this)[0].codvisitante
                    }

                    res=filtrar_data(filter_l,datos_contabilidad_amarillas);
                    resv=filtrar_data(filter_v,datos_contabilidad_amarillas);

                    reslrojas=filtrar_data(filter_l,datos_contabilidad_rojas);
                    resvrojas=filtrar_data(filter_v,datos_contabilidad_rojas);

                    html_a+="<tr>";
                        if(res.length>0){
                            html_a+="<td>"+res[0].cantidad+"</td>";
                            html_a+="<td>"+res[0].porcentaje_torneo+"</td>";
                            html_a+="<td>"+res[0].porcentaje_tafi+"</td>";
                        }else{
                            html_a+="<td>0</td>";
                            html_a+="<td>0</td>";
                            html_a+="<td>0</td>";
                        }

                        if(resv.length>0){
                            html_a+="<td>"+resv[0].cantidad+"</td>";
                            html_a+="<td>"+resv[0].porcentaje_torneo+"</td>";
                            html_a+="<td>"+resv[0].porcentaje_tafi+"</td>";
                        }else{
                            html_a+="<td>0</td>";
                            html_a+="<td>0</td>";
                            html_a+="<td>0</td>";
                        }
                    html_a+="</tr>";

                    //tabla de rojas 
                    html_r+="<tr>";
                        if(reslrojas.length>0){
                            html_r+="<td>"+reslrojas[0].cantidad+"</td>";
                            html_r+="<td>"+reslrojas[0].porcentaje_torneo+"</td>";
                            html_r+="<td>"+reslrojas[0].porcentaje_tafi+"</td>";
                        }else{
                            html_r+="<td>0</td>";
                            html_r+="<td>0</td>";
                            html_r+="<td>0</td>";
                        }

                        if(resvrojas.length>0){
                            html_r+="<td>"+resvrojas[0].cantidad+"</td>";
                            html_r+="<td>"+resvrojas[0].porcentaje_torneo+"</td>";
                            html_r+="<td>"+resvrojas[0].porcentaje_tafi+"</td>";
                        }else{
                            html_r+="<td>0</td>";
                            html_r+="<td>0</td>";
                            html_r+="<td>0</td>";
                        }
                    html_r+="</tr>";

                    res_a_l=filtrar_data(filter_l,datos_contabilidad_arbitraje);
                    res_a_v=filtrar_data(filter_v,datos_contabilidad_arbitraje);


                    //TABLA DE ARBITRAJE
                    html_arbi+="<tr>";

                        if((res_a_l[0].pago) =="No"){
                            color = "background: #f56e6e; color: black;";
                        }else{
                            color="";
                        }

                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codlocal+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_l[0].pago) + "</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codlocal+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_l[0].cantidad == null ? 0 : res_a_l[0].cantidad) + "</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codlocal+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_l[0].porcentaje_torneo  == null ? 0 : res_a_l[0].porcentaje_torneo) + "</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codlocal+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_l[0].porcentaje_torneo*porcentaje_interno_torneo/100) +"</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codlocal+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ ((res_a_l[0].cantidad * porcentaje_tafi)/100) +"</td>";
                        

                        if((res_a_v[0].pago) =="No"){
                            color = "background: #f56e6e; color: black;";
                        }else{
                            color="";
                        }

                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codvisitante+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_v[0].pago) + "</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codvisitante+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_v[0].cantidad == null ? 0 : res_a_v[0].cantidad) + "</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codvisitante+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_v[0].porcentaje_torneo  == null ? 0 : res_a_v[0].porcentaje_torneo) + "</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codvisitante+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ (res_a_v[0].porcentaje_torneo*porcentaje_interno_torneo/100) +"</td>";
                        html_arbi+="<td style='"+color+"' class='registro_arbitraje' rel='"+$(this)[0].codvisitante+"' calendario='"+$(this)[0].codigo_calendario+"'>"+ ((res_a_v[0].cantidad * porcentaje_tafi)/100) +"</td>";

                    html_arbi+="</tr>";

                    e++;
                });

                $("#tbl_listado_equipos tbody").html(html);
                $("#tbl_arbitraje tbody").html(html_arbi);
                $("#tbl_amarillas tbody").html(html_a);
                $("#tbl_rojas tbody").html(html_r);

                $(".tbl_resultado tbody tr").bind("click",function(){

                    $("#tbl_listado_equipos tbody tr").removeClass("trSelect");
                    $("#tbl_arbitraje tbody tr").removeClass("trSelect");
                    $("#tbl_amarillas tbody tr").removeClass("trSelect");
                    $("#tbl_rojas tbody tr").removeClass("trSelect");

                    $("#tbl_listado_equipos tbody tr").eq($(this).index()).addClass("trSelect");
                    $("#tbl_arbitraje tbody tr").eq($(this).index()).addClass("trSelect");
                    $("#tbl_amarillas tbody tr").eq($(this).index()).addClass("trSelect");
                    $("#tbl_rojas tbody tr").eq($(this).index()).addClass("trSelect");

                    local=$("#tbl_listado_equipos tbody tr").eq($(this).index()).find("td").eq(1).text();
                    visitante=$("#tbl_listado_equipos tbody tr").eq($(this).index()).find("td").eq(3).text();
                    fecha=$("#tbl_listado_equipos tbody tr").eq($(this).index()).find("td").eq(4).text();

                    $("#info_consulta_partido").html("<h5>Partido: "+local+" VS "+visitante+ " / "+fecha+"</h5>");
                });

                $(".registro_arbitraje").bind("click",function(){
                    $("#btn_registro_abono").click();
                    $("#codigo_equipo_arbitraje").val($(this).attr("rel"));
                    $("#codigo_calendario_arbitraje").val($(this).attr("calendario"));
                });



            },
            dataType: 'json'
        });
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
    function filtrar_data(filter, array){

        result = array.filter(function(item) {
            for (var key in filter) {
                if (item[key] === undefined || item[key] != filter[key])
                return false;
            }
            return true;
        });

        return result;
    }

</script>

<div class="card" style="padding:20px;" id="div_registro_torneo">

    <div class="form-floating mb-3">
        <select class="form-control" name="codigo_torneo" id="codigo_torneo" type="text"  lang="1" style="width: 300px;"></select>   
        <label for="name">Seleccione el Torneo </label>
    </div>

    <div id="div_reporte" style="display:none;">
 
        <div  id="div_info_inscripcion">

            <table class="table align-items-center mb-0" id="tbl_porcentajes" style="width: 30%;">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Costo</th>
                        <th>
                            Torneo 
                            <span id="porcentaje_torneo"></span>
                        </th>
                        <th>
                            Tafi 
                            <span id="porcentaje_tafi"></span>
                        </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <table class="table align-items-center mb-0" id="tbl_inscripcion" style="width: 30%;">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Valor de Inscripción Pagado
                        <span></span>
                        </th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>

        <hr>
        <h5>Seleccione la Ronda para consultar la Contabilidad.</h5>
        <div class="form-floating mb-3" id="div_ronda">
            <select class="form-control" name="codigo_ronda" id="codigo_ronda" type="text"  style="width: 300px;"></select>   
            <label for="name">Ronda</label>
        </div>

        <div id="div_cargando" class="spinner-border text-info" role="status" style="display:none;"><span class="sr-only"></span></div>

        <div id="info_consulta_partido"></div>

        <div id="reporte_resultado" style="" >
            <table class="table align-items-center mb-0 tbl_resultado" id="tbl_listado_equipos" style="width:500px;">
                <thead>
                    <tr>
                        <th colspan=5 style="text-align: center;">. </th>
                    </tr>
                    <tr>
                        <th colspan=5 style="text-align: center;"> Partidos </th>
                    </tr>

                    <tr>
                        <th>Partido</th>
                        <th>Local</th>
                        <th></th>
                        <th>Visitante</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <table class="table align-items-center mb-0 tbl_resultado" id="tbl_arbitraje" style="width:500px;">
                <thead>
                    <tr style="background: #dbdbdb;">
                        <th colspan=10 style="text-align: center;"> Arbitraje </th>
                    </tr>

                    <tr>
                        <th colspan=4 style="text-align: center;">Local</th>
                        <th colspan=4 style="text-align: center;">Visitante</th>
                    </tr>

                    <tr>
                        <th>¿Pagó?</th>
                        <th>Valor Pagado</th>
                        <th>Torneo</th>
                        <th>Arbitro <span class="porcentaje_arbitro"></span></th>
                        <th>Tafi</th>

                        <th>Pagó</th>
                        <th>Valor Pagado</th>
                        <th>Torneo</th>
                        <th>Arbitro <span class="porcentaje_arbitro"></span></th>
                        <th>Tafi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <table class="table align-items-center mb-0 tbl_resultado" id="tbl_amarillas" style="width:500px;">
                <thead>
                    <tr style="background: #f7f59e;">
                        <th colspan=6 style="text-align: center;"> Amarillas </th>
                    </tr>

                    <tr>
                        <th colspan=3 style="text-align: center;">Local</th>
                        <th colspan=3 style="text-align: center;">Visitante</th>
                    </tr>

                    <tr>
                        <th>Cantidad</th>
                        <th>Torneo</th>
                        <th>Tafi</th>

                        <th>Cantidad</th>
                        <th>Torneo</th>
                        <th>Tafi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <table class="table align-items-center mb-0 tbl_resultado" id="tbl_rojas" style="width:500px;">
                <thead>
                    <tr style="background: #f9adad;">
                        <th colspan=6 style="text-align: center;"> Rojas </th>
                    </tr>

                    <tr>
                        <th colspan=3 style="text-align: center;">Local</th>
                        <th colspan=3 style="text-align: center;">Visitante</th>
                    </tr>

                    <tr>
                        <th>Cantidad</th>
                        <th>Torneo</th>
                        <th>Tafi</th>

                        <th>Cantidad</th>
                        <th>Torneo</th>
                        <th>Tafi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

</div>

<!--BTN REGISTRO ABONO ABBITRAJE-->
<button type="button" class="btn bg-gradient-info" data-bs-toggle="modal" id="btn_registro_abono" data-bs-target="#registro_abono_arbitraje" style="display:none;"></button>

<div class="modal fade" id="registro_abono_arbitraje" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="width: 50%;">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> <img src="iconos/resultados.png" class="img_inscripcion"> Registro de Abonos (Arbitraje)</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

        <div class="modal-body">

            <form id="form_registro_abono" name="form_registro_abono" enctype="multipart/form-data" action="ajax/cambiar_reglamento.php" method="POST">
                <input type="hidden" name="codigo_equipo_arbitraje" id="codigo_equipo_arbitraje">
                <input type="hidden" name="codigo_calendario_arbitraje" id="codigo_calendario_arbitraje">


                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Valor de Abono</label>
                    <input class="form-control" name="valor_abono" type="text" value="" id="example-text-input" lang="1">
                </div>

                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Fecha de Pago</label>
                    <input class="form-control" name="fecha_pago" type="date" value="" id="example-text-input" lang="1">
                </div>
                
            </form> 
    
        </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn bg-gradient-info" id="btn_grabar_registro_abono">Grabar Arbitraje</button>
      </div>
    </div>
  </div>
</div>


<style>
    #reporte_resultado{
        display: flex;
        gap: 30px;
        overflow-x: scroll;
        overscroll-behavior-x: contain;
        scroll-snap-type: x proximity;
    }

    #tbl_inscripcion{
        height:241px;
        overflow: scroll;
    }

    #div_info_inscripcion{
        display: flex;
        gap: 50px;
    }

    td {
        text-align: center;
    }

    .trSelect{
        background: #3e66ff;
        color: #fff;
    }

    .iconos_tab, .img_inscripcion{
        width: 35px;
        height: 35px;
    } 

    @media (max-width: 800px)  {
        #tbl_porcentajes{
            width: 100% !important;
        }
    }
</style>