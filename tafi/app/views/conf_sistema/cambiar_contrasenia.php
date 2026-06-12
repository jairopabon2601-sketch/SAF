<script>
    $(document).ready(function() {
        $("#btn_cambio_contrasenia").bind("click",validar_formulario);
    });


    function validar_formulario(){
        
        sw=true;

        //se recorrer los inputs del formulario 
        $("#form_password input").each(function(){
            //se valida que no esten vacios
            if($(this).val()==""){
                $(this).focus();
                alert("Debe llenar todos los campos");
                sw=false;
                return false;
            }
        });
        
        if(sw){
            if($("#contrasenia_nueva").val()!=$("#contrasenia_conf").val() ){
                alert("Las contraseñas no coinciden");
                return false;
            }else{
                
                $.ajax({
                    url: "ajax/cambiar_contrasenia.php",
                    type: "POST",
                    data: $("#form_password").serialize(),
                    dataType: "json",
                    success: function(rest) {
                        console.log(rest);
                        
                        if(rest.respuesta==1){
                            alert(rest.mensaje);
                            window.location.href="dashboard.php";
                        }else{
                            alert(rest.mensaje);
                        }
                    }
                });

            }
        }
       
    }

</script>

<div class="card">
    <div style="margin: 40px;">

        <form id="form_password" name="form_password">

            <div class="form-group">
                <label for="example-password-input" class="form-control-label">Contraseña Actual</label>
                <input class="form-control" type="password" if="contrasenia_nueva" name="contrasenia_actual" id="example-password-input" lang="1">
            </div>

            <div class="form-group">
                <label for="example-password-input" class="form-control-label">Nueva Contraseña</label>
                <input class="form-control" type="password" id="contrasenia_nueva" name="contrasenia_nueva" id="example-password-input" lang="1">
            </div>

            <div class="form-group">
                <label for="example-password-input" class="form-control-label">Confirmar Contraseña</label>
                <input class="form-control" type="password" id="contrasenia_conf" name="contrasenia_conf" id="example-password-input" lang="1">
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-primary" id="btn_cambio_contrasenia">Cambiar Contraseña</button>
            </div>
        </form>

    </div>
</div>