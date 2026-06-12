
$(document).ready(function () {
    cargar_opciones();

  $(".btn_opcion_menu").on("click",function(){
    window.location="https://www.safenlinea.com/index.php?form="+$(this).attr('codigo_formulario');   
    return false;
	});
  
  if (codigo_formulario > 0) {
    filtro="codigo_proceso="+codigo_formulario;
    form=consultar_campo("tbl_procesos","concat(ruta,formulario)",filtro);
    cargar_view(form);
  }
});

function cargar_view(formulario){
	$("#div_principal_form").load(formulario);	
  return false;
}

function  cargar_opciones(){
	$.ajax({
      type: 'POST',
      async: false,
      url: 'ajax/cargar_opciones.php',
      success: function(data){
      	
        if(data.resultado==1){
        	html="";

        	$(data.opciones).each(function(){
        		html+="<a rel='"+$(this)[0].ruta+$(this)[0].formulario+"'  codigo_formulario='"+$(this)[0].codigo_proceso+"' class='btn_opcion_menu collapse-item'>"+$(this)[0].nombre+"</a>";
        	});

        	$("#div_opciones_menu").html(html);
        }
      },
      dataType: 'json'
  });
}

function mostrar_consulta(div,consulta,filtro){

  if (consulta==undefined) {
    $("#"+div).html("Debe agregar una consulta");
    return false;
  }


  if (filtro==undefined) {
    $("#"+div).html("Debe agregar un filtro");
    return false;
  }


  $("#"+div+"").append('<br>Cargando...'); 

  $.ajax({
      type: 'POST',
      async: false,
      url: 'ajax/mostrar_consulta.php',
      data:{
        consulta: consulta,
        filtro: filtro
      },
      success: function(data){
        console.log(data);
        data_div=data.mensaje;
        data=data.datos;
        if ( data.resultado==0 ) {
          data_div=data.mensaje;
        }else{
          $("#"+div+"").html(data);

      }
    },
    dataType: 'json'
  });
}

function listado_consulta(div,consulta,filtro,opcion,modo){

  data_div="";

  if (modo==undefined) {
    modo=false;
  }

  $.ajax({
      type: 'POST',
      async: modo,
      url: 'ajax/listado_consulta.php',
      data:{
        consulta: consulta,
        filtro: filtro,
        opcion: opcion
      },
      success: function(data){
        
        if ( data.resultado==0 ) {
          data_div=data.mensaje;
        }else{
          data_div=data.mensaje;
        }
      },
      dataType: 'json'
  });

  $("#"+div+"").html(data_div);

}

function rellenar_select(tabla,valor,etiqueta,filtro,id_select,seleccion,campos_orden){
  
  $.ajax({
    url:'ajax/listado_select.php',
    type:'post',
    dataType:'json',
    async:false,
    data:'tabla='+tabla+'&valor='+valor+'&etiqueta='+etiqueta+'&filtro='+filtro+'&campos_orden='+campos_orden,
    success:function(data,status){
     
      if(data==0){
        contenido='<option value="0">[No hay datos]<\/option>';
      }else{
        var contenido='<option value="0">[Seleccione]<\/option>';
        for(i=0;i<data.length;i++){
          contenido+='<option value="'+data[i][valor]+'">'+data[i][etiqueta]+'<\/option>';
        }

      }
      indicador=id_select.substring(0,1)=='[' ? '' : '#';
      
      $(indicador+id_select+' option').remove();
      $(indicador+id_select).append(contenido);
      if(seleccion!=''){
        $(indicador+id_select).val(seleccion);
      }
    }
  });
}

function actualizar_registro(tabla,formulario,filtro,modo){
  var datos = $("#"+formulario).serialize();
  datos+="&tabla="+tabla+"&filtro="+filtro+"&modo="+modo;

  $.ajax({
    url:'ajax/actualizar_registro.php',
    type:'post',
    dataType:'json',
    async:false,
    data:datos,
    success:function(data){
      //console.log(data);
      resultado = data
    }
  });
  return resultado;
}


function consultar_campo(tabla,nombre_campo,filtro){
  
  resultado = "";
  
  $.ajax({
    url:'ajax/consultar_campo.php',
    type:'post',
    dataType:'json',
    async:false,
    data:'filtro='+filtro+'&nombre_campo='+nombre_campo+'&tabla='+tabla,
    success:function(data){

      for (var i = 0; i < data.length ; i++) {
        resultado+=data[i];
        if (i < data.length-1) {
          resultado+=";";
        }
      }
    }
  });
  
  return resultado;
}

function noPuntoComa(event) {
  
    var e = event || window.event;
    var key = e.keyCode || e.which;

    if ( key === 110 || key === 190 || key === 188 ) {     
        
       e.preventDefault();     
    }
}


function formatNumber(n) {
    n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
}

function cargar_anio(selector,anio_inicio,anio_final){
    anios="";
    anios+="<option value='0'>Seleccione</option>";
    for (i=anio_inicio; i <= anio_final; i++) {
        anios+="<option value='"+i+"'>"+i+"</option>";
    }

    $("#"+selector).html(anios);
}

function diferenciaDias(fecha_desde, fecha_hasta){
  return fecha_hasta.diff(fecha_desde, 'days');
}

function abrir_url(url){
  window.location=url;
}

function get(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function limpiar_campos(formulario){
  for(i=0;i<document.forms[formulario].elements.length;i++){
    document.forms[formulario].elements[i].setAttribute("style","background-color:#FFFFFF");
  }
}

function validar_formulario(pagina, numero_formulario){
  retorno=true;
  console.log(document.forms[numero_formulario], numero_formulario);	

  for(i=0;i<document.forms[numero_formulario].elements.length;i++){
    
    if (document.forms[numero_formulario].elements[i].lang==pagina){
      switch(document.forms[numero_formulario].elements[i].type){

        case "text":

        case "date":

        case "hidden":

        case "email":

        case "file":

        case "number":

        case "textarea":
          tipo=1;
          break;

        case "select-one": 
          tipo=2;
          break;

        case "checkbox":
          tipo=3;
          break;
          
        case "radio":
          tipo=4;
          break;
      }
      if(tipo==1){
        if ((document.forms[numero_formulario].elements[i].value.trim()=="") || (document.forms[numero_formulario].elements[i].value=="0000-00-00")) {
          $.growl.error({ title: "Error", message: "Debe Digitar todos los Campos Requeridos" });
          document.forms[numero_formulario].elements[i].setAttribute("style","background-color:#FBCFD0");
          retorno=false;
          break;
        }
      }else if(tipo==2){
        if(document.forms[numero_formulario].elements[i].value==0){
          $.growl.error({ title: "Error", message: "Debe Digitar todos los Campos Requeridos" });
          document.forms[numero_formulario].elements[i].setAttribute("style","background-color:#FBCFD0");
          retorno=false;
          break;
        }				
      }else if(tipo==3){
        if (document.forms[numero_formulario].elements[i].checked==false){
          $.growl.error({ title: "Error", message: "Debe Digitar todos los Campos Requeridos" });
          document.forms[numero_formulario].elements[i].setAttribute("style","background-color:#FBCFD0");
          retorno=false;
          break;
        }				
      }else if(tipo==4){
        
        if ($("[name='" + "']:checked").val()==undefined){
          $.growl.error({ title: "Error", message: "Debe Digitar todos los Campos Requeridos" });
          document.forms[numero_formulario].elements[i].setAttribute("style","background-color:#FBCFD0");
          retorno=false;
          break;
        }			
      }
    }		
  }
  return retorno;
}


function validar_email(email) {
  var patron = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
  if(patron.test(email) == false) {
 $.growl.error({ title: "Error", message: "Formato de Email Invalido, verifique que tenga la estructura: nombre@servidor.com" });
     return false;
}else{
 return true;
}

}

function solo_numeros(e){
  var tecla;
  tecla = (document.all) ? e.keyCode : e.which;
  if((tecla == 0)||(tecla == 8)||(tecla == 9)||(tecla == 37)||(tecla == 39)||(tecla == 13)){
    return true;
  }
  var patron;
  patron = /[0-9]/
  var te;
  te = String.fromCharCode(tecla);
  return patron.test(te);
}

function solo_texto(e){
  var tecla;
  tecla = (document.all) ? e.keyCode : e.which;


  if((tecla == 8)||(tecla == 9)||(tecla == 32)||(tecla == 37)||(tecla == 39)||(tecla == 209)||(tecla == 241)){
    return true;
  }
  var patron;
  patron = /[A-Z,a-z,Ã±,Ã‘]/
  var te;
  te = String.fromCharCode(tecla);
  return patron.test(te);
}

function llenar_formulario(formulario, tabla, filtro){
  retorno="";
  $.ajax({
    type: "POST",
    url: "ajax/llenar_formulario.php",
    data: "tabla="+tabla+"&filtro="+filtro+"&formulario="+formulario,
    dataType: "json",   
    async: true, 
    success: function(datos){
      if(datos["resultado"]==1){   
        data=datos.datos[0];
        for(i=0;i<document.forms[formulario].elements.length;i++){
          campo=document.forms[formulario].elements[i].name;        
          $.each(data, function (key, value) {
            if (key==campo){
              document.forms[formulario].elements[i].value=value;
            }
          });
        }
        retorno=true;
      }else{
        retorno=false;
      }
    }
  });
  return retorno;
}

