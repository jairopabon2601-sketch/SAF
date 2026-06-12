function formato_todos_contra_todos(datos){
	html="<br><h6>Partidos Todos Contra Todos</h6>"
                    html+='<div class="sortable-list list_partidos">';
                        x=1;
                        total_fechas=datos.partidos.length;
			fechas_calendario=datos.partidos.length;
                    
                        $.each(datos.partidos, function(i, item) {
                            
                            html+="<div id='div_fechas'>";

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

				html+='<input type="hidden" value="'+x+'" name="numero_fecha[]">';
				html+='<input type="hidden" value="'+ronda+'" name="nombre_fecha[]">';

                               
                                $.each(item, function(i2, item2) {
                                    html+='<div id="div_cont_fechas"><div class="item">';
                                            html+='<div class="details">';
                                                html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item2[0].escudo+'" alt="Team Logo"></div>';
                                                html+='<span>'+item2[0].nombre_equipo+'</span>';

						html+='<input type="hidden" value="'+item2[0].codigo_equipo+'" name="local_fecha_'+x+'[]">';

                                            html+='</div>';

                                            html+=' VS';
                                            
                                            html+='<div class="details">';
                                                
                                                html+='<span>'+item2[1].nombre_equipo+'</span> ';
						html+='<div class="div_con_img"><img src="archivos/equipos/escudos/'+item2[1].escudo+'" alt="Team Logo"></div>';
						
						html+='<input type="hidden" value="'+item2[1].codigo_equipo+'" name="visitante_fecha_'+x+'[]">'; 


                                            html+='</div>';
                                        
                                        html+='</div>';

                                        html+='<div style="display: flex;">';
                                            html+='<input type="date" name="fecha_'+x+'_fecha_'+item2[0].codigo_equipo+'_'+item2[1].codigo_equipo+'" id="fecha_'+item2[0].codigo_equipo+'_'+item2[1].codigo_equipo+'" class="form-control">';
                                            html+='<input type="time" name="fecha_'+x+'_hora_'+item2[0].codigo_equipo+'_'+item2[1].codigo_equipo+'" id="hora_'+item2[0].codigo_equipo+'_'+item2[1].codigo_equipo+'" class="form-control">';
                                        html+='</div>';

					html+='<div class="form-floating mb-3">';
                                                html+='<select class="form-control" name="sede_'+x+'_'+item2[0].codigo_equipo+'_'+item2[1].codigo_equipo+'" id="codigo_sede" type="text"  style="width: 100%;">';
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
	return html;
}