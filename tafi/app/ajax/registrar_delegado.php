<?php
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
    include("../php/usuarios.php");

    $conexion=new conexion_db();
    $user =new usuarios();

    //SE PROCEDE A CREAR EL DELEGADO 
    $datos=array();
    $datos["nombre_delegado"]=$_POST["nombre_delegado"];
    $datos["numero_documento"]=$_POST["numero_documento"];
    $datos["email"]=$_POST["email"];
    $datos["celular"]=$_POST["celular"];
    $datos["permite_crear_equipo"]=1;

    $resultado=$conexion->insertar("tbl_tafi_delegados", $datos);

    if($resultado){
        $codigo_delegado=$conexion->insert_id();
        //se le asigan el usuario
        $data=array();
        $data["codigo_origen"]=$codigo_delegado;
        $data["codigo_tipo_usuario"]=3;
        $data["usuario"]=$_POST["numero_documento"];
        $data["email"]=$_POST["email"];

        $user->registrar_usuario($data);

        $retorno["resultado"]=1;
        $retorno["mensaje"]="Delegado creado correctamente.";
        
    }else{
        $retorno["resultado"]=0;
        $retorno["mensaje"]="No se pudo crear el delegado, es posible que el documento de identidad ya exista.";
    }
        
    echo json_encode($retorno);

?>