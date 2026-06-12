<script language="JavaScript" type="text/javascript">
  var resultado;
  $(document).ready(function() {

    $("#codigo_empresa").html(rellenar_select("tb_rrhh_empresas","codigo_empresa","nombre_empresa","","codigo_empresa"));
   /* $("#codigo_proceso").change(function(){
      codigo_proceso=$(this).val();
      filtro="usu.tipo_usuario='" + tipo_usuario + "' and pro.codigo_proceso='"+ codigo_proceso +"'";
      $("#codigo_area").html(imprimir_combo_consulta("desplegable_areas_tipo_usuario", filtro));;
    });
    
    $("#ayuda_tipo_documento").click(function(){
      $( "#dialog_modal" ).dialog("open");
    });

    if (tipo_sqrp==5) {      
      $("#codigo_causa").attr('lang','');
    }else{
      $("#codigo_causa").attr('lang','1');        
    }

    if (tipo_sqrp==4) {
        $(".p_peticion").show();
      }else{
        $(".p_peticion").hide();
      }

    $('#btn_enviar').bind('click', enviar_formulario);


    if(tipo_sqrp){
      $("#tipo_sqrp").val(tipo_sqrp);
      $("#tipo_sqrp").attr('disabled',true);
    }

    $('#btn_siguiente').bind('click', function(){

      var datos='tipo_usuario='+ $("#tipo_usuario").val();
      datos+="&documento_identidad='"+ $("[name='documento_identidad']").val() + "'";

      $.ajax({
        type: "POST",
        async: false,
        url: "modulos/sgc/ajax/ajax_cargar_datos_adicionales.php",
        data: datos,
        dataType: "html",
        beforeSend: function(){
        },
        error: function(){
          alert("error petición ajax");
        },
        success: function(data){
          $("#div_datos_adicionales").html(data);
        }
      });

      var ho=$('#tipo_usuario').val();
      switch (ho)
      {
        case '1':
        tipo='1';break;
        case '2':
        tipo='1';break;
        case '3':
        tipo='';break;
        case '4':
        tipo='1';break;
        case '5':
        tipo='';break;
        case '6':
        tipo='2';break;
        case '7':
        tipo='2';break;
        case '8':
        tipo='';break;
        case '9':
        tipo='1';break;
      }
      if ($('#tipo_usuario').val()!=0 && $("[name='documento_identidad']").val()!="" ){
        resultado=cargar_datos(tipo);
        if(resultado==1){
          $("#div_registro_datos_personales").show();
          $("[name='nombres']").attr('readonly',true) 
          $("[name='apellidos']").attr('readonly',true)
          $("[name='telefono_fijo']").focus();
        }else{
          $("#div_registro_datos_personales").show();
          limpiar();
          $("[name='nombres']").attr('readonly',false)
          $("[name='apellidos']").attr('readonly',false)
          $("[name='nombres']").focus();          
        }
      }else{
        alert("Llenar los campos obligatorios");
      }
      var ts=".t"+$("#tipo_sqrp").val();
      $(".reg").show();
      $(ts).hide();
      $(".reg").find("textarea").attr('lang', '0');
    });
    $('#tipo_sqrp').change(function(){
      if ($(this).val()==5) {
        $("#codigo_causa").attr('lang','');
      }else{
        $("#codigo_causa").attr('lang','1');        
      }
      limpiar();
      $("#div_registro_datos_personales").hide();

      if ($(this).val()==4) {
        $(".p_peticion").show();
      }else{
        $(".p_peticion").hide();
      }

    });

    $('#tipo_usuario').change(function(){
      $(".ocul").hide();
      var tu=".p"+$(this).val();
      $(tu).show();
      limpiar();
      $("#div_registro_datos_personales").hide();

    });
    $( "#dialog_modal" ).dialog({
      autoOpen:false,
      width:670,
      height: 500,
      modal: true,
    });

    $("#tipo_usuario").bind("change", function(){
      tipo_usuario=$(this).val();

      filtro="usu.tipo_usuario='" + tipo_usuario +"'";
      console.log(filtro);
      $("#codigo_area").html(imprimir_combo_consulta("desplegable_areas_tipo_usuario", filtro));;
    });

    $("#codigo_causa").html(imprimir_combo_consulta("desplegable_sqrp_causas", "causa.tipo=2"));

    $("#codigo_causa").bind("change", function(){
      if($(this).val()==1){
        $("#otra_causa").show("slow");
      }else{
        $("#otra_causa").hide();
        $("[name='causa']").val($('#codigo_causa option:selected').text());
      }
    });

    $("#subcontenedor").attr('style','width:100%');
    $("#contenido").attr('style','width:80%');


  });
var devolver_res;
function cargar_datos(valor){
  var res=0;
  filtro="documento_identidad='"+ $("[name='documento_identidad']").val() + "'";
  filtro+=" AND tipo= '"+ valor +"'";
  $.ajax({
    type: 'POST',
    async: false,
    url: 'ajax/generar_json_listado_consulta.php',
    data: {
      codigo_consulta: "datos_solicitante_sqrp",
      filtro: filtro,
      agrupacion: ""
    },
    success: function(data){
      if(data==""){
        resultado=0;
      }else{
        dt_datos=data;
        $(dt_datos).each(function(){
          $("[name='nombres']").val($(this)[0].nombres);
          $("[name='apellidos']").val($(this)[0].apellidos);
        });
        res=1;
        resultado=1;
      }
      devolver_res=res;
    },
    dataType: 'json'
  });
  return devolver_res;
}
function limpiar(){
  $("[name='nombres']").val("");
  $("[name='apellidos']").val("");
  $("[name='codigo_proceso']").val(0);
  $("[name='codigo_area']").val(0);
}
function enviar_formulario(){
  resultado=validar_texto("1", "0");
  if(resultado==true){
    $("#tipo_sqrp").attr('disabled',false);
    document.formulario.submit(); 
  }
  */
});
</script>

<div>
  <form name="formulario" action="pattern.php?form=sqrp_solicitudes_guardar_solicitud.php" enctype="multipart/form-data" method="post">
    <div>  
    </div>
    <div id="div_registro_datos_personales" style="">
      <div id="div_datos_personales"></div>
      <p>
        <label>Empresa Solicitante</label>
        <select name='codigo_empresa' id='codigo_empresa' lang='1'>
        </select>
      </p>
      <p>
        <label class="req">Riesgo:*</label>
        <input name="riesgo" type="text" size="30" maxlength="50" lang="1">
      </p>
      <p>
        <label class="req">Otros Pagos:*</label>
        <input name="otros_pagos" id="otros_pagos" type="text" size="30" maxlength="50" lang="1">
      </p>
      <p>
        <label class="req">Donde se presenta:</label>
        <input name="donde_se_presenta" id="donde_se_presenta" type="text" size="30" maxlength="30" lang="1">
      </p>
      <p>
        <label class="req">Por quien pregunta:*</label>
        <input name="por_quien_pregunta" id="por_quien_pregunta" type="text" size="40" maxlength="255" lang="1">
      </p>
      <p>
        <label class="req">Área Involucrada*</label>
        <select name='codigo_area' id='codigo_area' lang='1'></select>
      </p>
      
      <p>
        <label class="req">Indique la Causa*</label>
        <select name='codigo_causa' id='codigo_causa' lang='1'>
        </select>
      </p>
      <p id='otra_causa' style='display: none;'>
        <label class='req'>Indique Cual es la Causa:*</label>
        <input name="causa" type="text" size="50" maxlength="255">
      </p>
      <p>
        <input id="btn_enviar" type="button" value="Registrar >>">
      </p>
    </div>
  </form>
</div>


