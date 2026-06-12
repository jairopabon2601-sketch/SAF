function formato_grupo(datos){
	grupo=0;
	fechas_calendario=datos.grupos.length;
                    html="<br><h6>Partidos por Grupo</h6>";
			
                    $.each(datos.grupos, function(i, item) {
			

                        html+='<div class="sortable-list list_partidos">';
                            html+='<div class="div_fechas_grupos">';
                                
                                
                                html+="<div class='div_fase_grupos'>";

                                    html+="<div>";
					nombre_grupo=grupoLetra(i);

                                        html+='<h6>Grupo '+nombre_grupo+'</h6>';

					html+='<input type="hidden" value="'+nombre_grupo+'" name="nombre_grupo[]">';

                                        $.each(item, function(i2, item2) {
                                            html+='<div id="div_cont_fechas"><div class="item">';
                                                    html+='<div class="details">';
                                                        html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item2.escudo+'" alt="Team Logo"></div>';
                                                        html+='<span>'+item2.nombre_equipo+'</span>';

							html+='<input type="hidden" value="'+item2.codigo_equipo+'" name="grupo_'+nombre_grupo+'_equipo[]">';

                                                    html+='</div>';
                                            html+='</div></div>';
                                        });
                                    html+='</div>';
                                    
                                    html+="<div style='    margin-left: 20px;'>";
                                        html+='<h6>Partidos</h6>';

                                        total_fechas=datos.partidos[i].length;
			
                                        x=1;
                                        $.each(datos.partidos[grupo], function(i, item_partido) {
                            
                                            html+="<div class='div_fechas_grupos'>";

                                                ronda="";
                                                if(datos.cantidad_rondas==2){
                                                    if(x<=total_fechas/2){
                                                        ronda="Fecha "+x+" - Ida";
                                                    }else{
                                                        ronda="Fecha "+x+" - Vuelta";
                                                    }
                                                }else{
                                                    ronda="Fecha "+x;
                                                    
                                                }
							
                                                html+="<h6> "+ronda+"</h6>";
						fecha_equipo=ronda.replace(/\s+/g, '');

						html+='<input type="hidden" value="'+x+'" name="numero_fecha_grupo_'+nombre_grupo+'[]">';
						html+='<input type="hidden" value="'+ronda+'" name="nombre_fecha_grupo_'+nombre_grupo+'[]">';

                                                $.each(item_partido, function(i2, item2_partido) {
                                                   
                                                    html+='<div id="div_cont_fechas"><div class="item">';

                                                            html+='<div class="details">';
                                                                html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item2_partido[0].escudo+'" alt="Team Logo"></div>';
                                                                html+='<span>'+item2_partido[0].nombre_equipo+'</span>';

								html+='<input type="hidden" value="'+item2_partido[0].codigo_equipo+'" name="local_fecha_'+x+'_'+nombre_grupo+'[]">';

                                                            html+='</div>';
								

                                                            html+=' VS';
                                                            
                                                            html+='<div class="details">';
                                                                
                                                                html+='<span>'+item2_partido[1].nombre_equipo+'</span> ';
								html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item2_partido[1].escudo+'" alt="Team Logo"></div>';

								html+='<input type="hidden" value="'+item2_partido[1].codigo_equipo+'" name="visitante_fecha_'+x+'_'+nombre_grupo+'[]">';
                                                            html+='</div>';
                                                        
                                                        html+='</div>';

                                                        html+='<div style="display: flex;">';
                                                            html+='<input type="date" name="'+nombre_grupo+x+'_fecha_'+item2_partido[0].codigo_equipo+'_'+item2_partido[1].codigo_equipo+'" id="fecha_'+item2_partido[0].codigo_equipo+'_'+item2_partido[1].codigo_equipo+'" class="form-control">';
                                                            html+='<input type="time" name="'+nombre_grupo+x+'_hora_'+item2_partido[0].codigo_equipo+'_'+item2_partido[1].codigo_equipo+'" id="hora_'+item2_partido[0].codigo_equipo+'_'+item2_partido[1].codigo_equipo+'" class="form-control">';
                                                        html+='</div>';

						html+='<div class="form-floating mb-3">';
                                                html+='<select class="form-control" name="'+nombre_grupo+x+'_sede[]" id="codigo_sede" type="text"  style="width: 100%;">';
                                                    html+='<option value="0">Seleccione la Sede</option>';
                                                    $.each(sedes, function(i, item_sedes) {
                                                        
                                                            html+='<option value="'+item_sedes.codigo_sede+'">'+item_sedes.nombre+'</option>';
                                                        
                                                    
                                                    });
                                                html+='</select>';   
                                                html+='<label for="name">Seleccione la Sede </label>';
                                            	html+='</div>';


                                                    html+='</div>';
                                                });

                                            html+='</div>';
                                            x++; 
                                        });

                                    html+='</div>';
                                html+='</div>';

                            html+='</div>';
                        html+='</div><br>';

                        grupo++;
                    });
	return html;

}

 function  grupoLetra(index){
        //abecedario array 
        var abecedario = new Array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P");
        return abecedario[index];
    }