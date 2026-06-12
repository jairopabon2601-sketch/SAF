<?php

?>

<script type="text/javascript">

	var codigo_vacante=get("codigo_vacante");

	$(document).ready(function(){
		$("#btn_consultar_candidato").bind("click",consultar_candidato);
	});

	function consultar_candidato(){

		mostrar_consulta("div_datos_candidato","");

	}


</script>
<h2>Candidatos</h2>

<br>

<div class="input-group mb-3">
  <input type="text" class="form-control" placeholder="Numero de Documento" aria-label="Recipient's username" aria-describedby="button-addon2" name="imp_consulta">
  <button class="btn btn-outline-secondary" type="button" id="btn_consultar_candidato">Consultar</button>
</div>

<div id="div_datos_candidato" >

</div>