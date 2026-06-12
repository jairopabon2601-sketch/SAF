<style type="text/css">
.ul_menu_opciones_estadisticas{
		list-style:none;
		margin:0;
		padding:0;
		width: 100%;
		margin-bottom: 15px;
	}

	.ul_menu_opciones_estadisticas li{
		text-decoration: none;
		display: inline-block;
		border: 1px solid #efefef;
		border-radius: 8px;
		padding: 4px;
		background-color:#eee;
		box-shadow: 0 1px 10px rgba(0,0,0,0.1);
		font-size: 20px;		
	}

	.ul_menu_opciones_estadisticas li a{
		text-align: center;
	}

	.ul_menu_opciones_estadisticas .seleccionado{
		background-color:#191970;
		font-weight: bold;
		color: #fff;
	}

    #div_listado table{
        display: inline-table;
    }


</style>
<script type="text/javascript">
	var valor_diario;
	$(document).ready(function() {
		cargar_listado();

		$("#btn_crear_usu").bind("click",cargar_select_dialog);

		$("#codigo_tipo_usuario").on("change",function(){
			res=consultar_campo("tbl_usuarios_tipos","tabla_origen,campo_tabla_origen","codigo_tipo_usuario=1");
			resultado=res.split(";");

			tabla_origen=resultado[0];
			campo_tabla=resultado[1];

			console.log(tabla_origen); 

			rellenar_select(tabla_origen,campo_tabla,"concat(nombres,' ',apellidos)","","codigo_origen");

		});

	});

	function diferenciaDias(fecha_desde, fecha_hasta){
		let fecha1 = new Date(fecha_desde);
		let fecha2 = new Date(fecha_hasta);
		let diferencia = fecha2.getTime() - fecha1.getTime();
		let diasDeDiferencia = diferencia / 1000 / 60 / 60 / 24;
		return diasDeDiferencia; 
	}

	function cargar_listado(){
		listado_consulta("div_listado","listado_deudores_calificacion","");
	}

	function cargar_select_dialog(){
		rellenar_select("tbl_perfiles","codigo_perfil","nombre","","codigo_perfil");

		rellenar_select("tbl_usuarios_tipos","codigo_tipo_usuario","nombre","","codigo_tipo_usuario");
	}


</script>

<div style="display: flex;">
	<div style="width: 50%"><h3>Estadísticas</h3></div>
</div>
<hr>
<div id="menu_tabs">
    <ul class="ul_menu_opciones_estadisticas">
        <li style="width: 46%;" id="btn_scoring">
                Scoring de Deudores
        </li>
    </ul>
    <div id="div_scoring">
        <div id="div_listado"></div>
    </div>
</div>  

