<?php

    function render_template($template, $data){
        extract($data);
        require($template);
    }

    function datos_formulario($codigo_proceso){

        $datos_usuario=array();
        
        if($codigo_proceso>0){
            
            session_start();
	        require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");

            $conexion =new conexion_db();

            $sql="SELECT 
            
            concat(p.ruta,'/',p.formulario) as ruta,
            nombre, 
            icono

            FROM  tbl_tafi_procesos p
            
            WHERE p.codigo_proceso=".$codigo_proceso;

            $resultado=$conexion->ejecutar_sql($sql);
            $datos_usuario=$resultado->fetch_all(MYSQLI_ASSOC);	
            $datos_usuario=$datos_usuario[0];
            
        }

        return $datos_usuario;


    }

?>