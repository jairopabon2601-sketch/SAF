
<script id="tmp_hoja_vida" type="text/template">
    <table style="width: 100%;" id="tbl_hoja_vida">
        <tr style="text-align: center;">
            <td colspan="2"><h3>{0} {1}</h3></td>
        </tr>
        <tr style="text-align: center;">
            <td colspan="2"><h4>{equipo_actual}</h4></td>
        </tr>
        <tr class="tr_tabla">
            <td>FECHA DE NACIMIENTO</td>
            <td>{2}</td>
        </tr>
        <tr class="tr_tabla">
            <td>TIPO DE DOCUMENTO</td>
            <td>{tipo_documento}</td>
        </tr>
        <tr class="tr_tabla">
            <td>DOCUMENTO</td>
            <td>{4}</td>
        </tr>
        <tr class="tr_tabla">
            <td>CELULAR</td>
            <td>{6}</td>
        </tr>
        <tr class="tr_tabla">
            <td>CORREO</td>
            <td>{5}</td>
        </tr>
    </table>
</script>

<script>
    const codigo_jugador = get("codigo_jugador");
    ruta_documento = "";
    
    $(document).ready(function() {
        datos=consultar_campo("tbl_tafi_jugadores","nombres, apellidos, fecha_nacimiento,tipo_documento,numero_documento, email,celular,ruta_foto, codigo_jugador, ruta_documento","codigo_jugador='"+codigo_jugador+"'");
        datos=datos.split(";");

        tipo_documento=consultar_campo("tbl_tafi_tipo_documento","tipo","codigo_tipo_documento='"+datos[3]+"'");
        codigo_equipo=consultar_campo("tbl_tafi_equipos_jugadores","codigo_equipo","codigo_jugador='"+datos[8]+"' and activo=1");
        equipo_actual=consultar_campo("tbl_tafi_equipos","nombre_equipo","codigo_equipo='"+codigo_equipo+"'");

        foto=datos[7];
        ruta_documento=datos[9];
        $("#ifr_documento").attr("src","archivos/jugadores/documento_identidad/"+ruta_documento);
        $("#img_jugador").attr("src","archivos/jugadores/fotos/"+foto);

        tbl=$("#tmp_hoja_vida").text();
        datos.forEach(function(valor, indice, array) {
            tbl=tbl.replace("{"+indice+"}",valor.toUpperCase());
        });

        tbl=tbl.replace("{tipo_documento}",tipo_documento.toUpperCase());
        tbl=tbl.replace("{equipo_actual}",equipo_actual);

        $("#div_datos_jugador").html(tbl);

        cargar_trayerctoria_jugador();
    });

    function cargar_trayerctoria_jugador(){
        $.ajax({
            url: "ajax/jugador/jugador_trayectoria.php",
            type: "POST",
			dataType: "json",
            data: {
				codigo_jugador: codigo_jugador
			},
            success: function(res) {

                if (res.resultado == 1) {
                    
                    html="";
                    $(res.datos).each(function(i, v) {
                        html+="<tr>";
                        html+="<td>";

                        html+="<div class='d-flex px-2 py-1'>";
                        html+="<div>";
                        html+="<img src='archivos/equipos/escudos/"+v.escudo+"' class='avatar avatar-sm me-3'>";
                        html+=" </div>";
                        html+="<div class='d-flex flex-column justify-content-center'>";
                            html+="<h6 class='mb-0 text-xs'>"+v.nombre_equipo+"</h6>";
                        html+="</div>";
                        html+="</div>";
                        
                        html+="</td>";
                        html+="<td>"+v.nombre_torneo+"</td>";
                        html+="<td class='text-center'>"+v.numero+"</td>";
                        html+="<td class='text-center'>0</td>";
                        html+="<td class='text-center'>0</td>";
                        html+="<td class='text-center'>0</td>";
                        html+="<td class='text-center'>0</td>";
                        html+="</tr>";
                    });

                    $("#tbl_trayectoria tbody").html(html);
    	
				} else {
					$("#tbl_datos_trayectoria").html(res.mensaje);
				}
            }
        });	

    }

</script>

<div class="card div_content">
    <div id="div_hoja_vida">
        <div id="div_datos_jugador"></div>
        <div id="div_img">
            <img src="" id="img_jugador" style="" class="img-fluid border-radius-lg">
        </div>
    </div>

    <div style="margin-top: 14px;" id="div_acciones">
        <button type="button" class="btn btn-outline-info">VER CARNET</button>
        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal_docs">VER DOCUMENTO</button>
    </div>

    <div id="div_trayectoria">
        <p>
            <h4>TRAYECTORIA DE TORNEOS</h4>
        </p>

        <div id="tbl_datos_trayectoria">

        <div class="table-responsive">
            <table class="table align-items-center mb-0" id="tbl_trayectoria">
                <thead>
                    <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">EQUIPO</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">TORNEO</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DORSAL</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">PARTIDOS JUGADOS</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">GOLES</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">AMARILLAS</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ROJAS</th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>


<!---------MODAL DOCUMENTO---------->
<div class="modal fade" id="modal_docs" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <iframe style="width:100%;height:600px;" id="ifr_documento">

        </iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
    .modal-dialog {
        max-width: 100% !important;
    }

    #div_trayectoria h4{
        color: #21277e;
        font-family: sans-serif;
        text-align: center;
    }

    #img_jugador{
        object-fit: cover;
        width: 50%;
        height:100%;
    }

    #div_hoja_vida{
        display: grid;
        grid-template-columns: 70% 30%;
        width: 70%;
        margin: auto;
    }

    #div_acciones{
        width: 70%;
        margin: auto;     
    }

    .div_content{
        padding: 35px;
    }

    #div_img{
        max-height: 220px;
        display: flex;
    }

    #tbl_hoja_vida h4,h3{
        color: #21277e;
        font-family: sans-serif;
    }

    .tr_tabla{
        height: 35px;
    }

    @media only screen and (max-width: 800px) {
        #div_hoja_vida{
            display: flex;
            justify-content: center;
            align-items: center;
            justify-items: start;
            flex-direction: column-reverse;
            width: 100%;
        }

        #div_acciones{
            width: 100%;
            margin: auto;     
        }
        #div_img{
            display: block;
            margin-bottom: 12px;
            text-align: center;
        }
    }
</style>