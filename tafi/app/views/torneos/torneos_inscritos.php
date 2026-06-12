<script>  
    $(document).ready(function() {
       listado_equipos();
    });

    function listado_equipos(){
        const filtro="del.codigo_delegado='"+codigo_origen+"'";

        listado_consulta("div_equipos","listado_equipos_torneos",filtro,1);
    }

    function detalles_torneo(codigo){
        window.location.href= "dashboard.php?proc=10&codigo="+codigo;
    }

</script>

<div class="card">
    <div id="div_equipos" class="table-responsive"></div>    
</div>
