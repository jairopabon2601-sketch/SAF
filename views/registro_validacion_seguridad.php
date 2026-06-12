<style>

  .btn_sol{

    min-width: 46px;
    text-align: center;
    
    font-size: 1em;

    width: 280px;
    height: 45px !important;

    
    padding: 10px;
    
    border: 1px solid #B0281A;
    
    color: white;
    
    margin-top: 5px;
    margin-left: 5px;
    text-shadow: 0 1px rgba(0, 0, 0, 0.3);
    
    background-color: #3671ba;
    background-image: -webkit-gradient(linear,left top,left bottom,from(#458ade),to(#3671ba));
    background-image: -webkit-linear-gradient(top,#458ade,#3671ba);
    background-image: -moz-linear-gradient(top,#458ade,#3671ba);
    background-image: -ms-linear-gradient(top,#458ade,#3671ba);
    background-image: -o-linear-gradient(top,#458ade,#3671ba);
    background-image: linear-gradient(top,#458ade,#3671ba);
    
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.2);
    -moz-box-shadow: 0 1px 1px rgba(0,0,0,0.2);
    -ms-box-shadow: 0 1px 1px rgba(0,0,0,0.2);
    -o-box-shadow: 0 1px 1px rgba(0,0,0,0.2);
    box-shadow: 0 1px 1px rgba(0,0,0,0.2);

  }

  .btn_opciones{
    margin-right: 25px;
    font-size: 0.7em;
    text-align: center;
    float:right
  }

</style>

<script>
  
 /* <?php
    //echo "var cod_usuario='".$_SESSION['codigo_origen'] . "';\n"; 
  ?>
  */

  $(document).ready(function(){

    //alert(cod_usuario);
    listado_solicitudes();
    //$("#div_datos_sol").find("td").style('width', '100px');
    $("#div_datos_sol thead tr").find('th').eq(0).attr("style","width:37px");
    
  });

  function listado_solicitudes(){

    //filtro="reg.codigo_responsable="+cod_usuario;
    // imprimir_listado_consulta("div_datos_sol", "listado_acciones_funcionario", filtro ,1);
  }
</script>

<div class="box_gray">
  <input type="button" onclick="location.href='pattern.php?form=1100'" class="btn_sol" value="AGREGAR NUEVA ACCIÓN"/>
</div>

<div class="box_white"> 
  <p>
    <h1><img src="iconos/solicitudes_compra.png" align="absmiddle">Mis Acciones</h1>
  </p>
  <p>
    A continuación se muestra el listado de solicitudes que usted a realizado, desde aquí podrá consultar en que estado se encuentra cada una de ellas, agregar cotizaciones, entre otros.
  </p>

  <div id='div_datos_sol'>
  </div>
</div>