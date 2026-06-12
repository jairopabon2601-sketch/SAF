<script src="js/jquery.growl.js" type="text/javascript"></script>
<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	
	$(document).ready(function() {
		cargar_listado();

		$("#btn_crear_usu").bind("click",function(){
			$("#btn_grabar_usuarios").attr("rel","1");
			cargar_select_dialog();
		});

		$("#codigo_tipo_usuario").on("change",function(){
			res=consultar_campo("tbl_usuarios_tipos","tabla_origen,campo_tabla_origen","codigo_tipo_usuario=1");
			resultado=res.split(";");

			tabla_origen=resultado[0];
			campo_tabla=resultado[1];

			rellenar_select(tabla_origen,campo_tabla,"concat(nombres,' ',apellidos)","","codigo_origen");

		});

		$("#btn_grabar_usuarios").bind("click",function(){
			if($(this).attr("rel")=="2"){
				grabar_editar_usuarios();
			}else{
				grabar_usuarios();
			}
		});
		
	});

	function editar_usuario(codigo_usuario){
		document.reg_usuarios.reset();
		$("#modal_usu_reg").modal();
		cargar_select_dialog();
		$("#modal_usu_reg").attr('rel',codigo_usuario);	
		limpiar_campos("reg_usuarios");	
		llenar_formulario("reg_usuarios","tbl_usuarios","codigo_usuario="+codigo_usuario);
		filtro = "codigo_tipo_usuario="+$("#codigo_tipo_usuario").val();
		res=consultar_campo("tbl_usuarios_tipos","tabla_origen,campo_tabla_origen",filtro);
		resultado=res.split(";");
		tabla_origen=resultado[0];
		campo_tabla=resultado[1];
		rellenar_select(tabla_origen,campo_tabla,"concat(nombres,' ',apellidos)","","codigo_origen");
		llenar_formulario("reg_usuarios","tbl_usuarios","codigo_usuario="+codigo_usuario);
		$("#btn_grabar_usuarios").attr("rel","2");

	}

	function grabar_editar_usuarios(){
		codigo_usuario=$("#modal_usu_reg").attr('rel');
		valor = validar_formulario('1','reg_usuarios');
		modo = 2;
		tabla = 'tbl_usuarios';
		filtro='codigo_usuario='+codigo_usuario;
		
		if(valor!=false){
			resultado = actualizar_registro(tabla,"reg_usuarios",filtro,2);
			if(resultado.resultado==1){
				$.growl.notice({ message: resultado.mensaje });
				cargar_listado();
				$("#modal_usu_reg").modal('hide');
			}
			else{
				$.growl.error({ message: resultado.mensaje });
			}
		}
	}

	function cargar_listado(){
		listado_consulta("div_listado","listado_usuarios","",1);
	}

	function cargar_select_dialog(){
		rellenar_select("tbl_perfiles","codigo_perfil","nombre","","codigo_perfil");

		rellenar_select("tbl_usuarios_tipos","codigo_tipo_usuario","nombre","","codigo_tipo_usuario");
	}

	function gruardar_usuario(){
		campos=$("#reg_usuarios").serialize();

		console.log(campos);

		$.ajax({
          type: 'POST',
          async: false,
          url: 'ajax/registrar_usuarios.php',
          data: campos,
          success: function(data){
            alert(data);
            $("#modal_reg_ahorrador").modal('hide');
            //window.location.reload(true);
          },
          dataType: 'text'
     	});

	}

</script>

<div style="display: flex;">
	<div style="width: 50%"><h3>Usuarios</h3></div>
	

	<div style="width: 50%;float: right;text-align: end;">
		<button type="button" class="btn btn-success" id="btn_crear_usu" data-toggle="modal" data-target="#modal_usu_reg">
		  Crear Usuario <i class="fi-rr-user-add"></i>
		</button><br><br></div>
</div>
			

<div id="div_listado"></div>


<!-- Modal -->
<div class="modal fade" id="modal_usu_reg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Registro de Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form class="reg_usuarios" name="reg_usuarios" id="reg_usuarios">	

	        <div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Email Usuario</span>
			  </div>
			  <input type="text" name="usuario" class="form-control" lang="1" placeholder="" aria-label="Username" aria-describedby="basic-addon1">
			</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Perfil</span>
			  </div>
			  <select class="form-control" lang="1" name="codigo_perfil" id="codigo_perfil"></select>		
			</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Tipo Usuario</span>
			  </div>
			  <select class="form-control" lang="1" name="codigo_tipo_usuario" id="codigo_tipo_usuario"></select>		
			</div>

			<div class="input-group mb-3">
			  <div class="input-group-prepend">
			    <span class="input-group-text" id="basic-addon1">Usuario</span>
			  </div>
			  <select class="form-control" lang="1" name="codigo_origen" id="codigo_origen"></select>		
			</div>
			
		</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" rel="" id="btn_grabar_usuarios">Grabar</button>
      </div>
    </div>
  </div>
</div>