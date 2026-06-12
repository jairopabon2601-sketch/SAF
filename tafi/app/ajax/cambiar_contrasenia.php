<?php
    include("../php/usuarios.php");

    $user =new usuarios();


    if($user->validar_contrasenia_usuario($_SESSION["codigo_usuario"], $_POST["contrasenia_actual"])){
        
        if($user->cambiar_contrasenia($_SESSION["codigo_usuario"], $_POST["contrasenia_nueva"])){
            $retorno["respuesta"]=1;
            $retorno["mensaje"]="La contraseña se ha cambiado correctamente";
        }else{
            $retorno["respuesta"]=0;
            $retorno["mensaje"]="No se pudo cambiar la contraseña";
        }
    }else{
        $retorno["respuesta"]=0;
        $retorno["mensaje"]="La contraseña actual no coincide";
    }

    echo json_encode($retorno);
?>