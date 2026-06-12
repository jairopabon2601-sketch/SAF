<?php

    function validar_peso_archivo($nombre_archivo){    
        $resultado="";
        global $_FILES;

        $nombre_archivo = $_FILES[$nombre_archivo]['name'];
        $tipo_archivo = $_FILES[$nombre_archivo]['type'];
        $tamano_archivo = $_FILES[$nombre_archivo]['size'];
        
        if ( $tamano_archivo > 300000 ) {
            $resultado= "El tamaño de los archivos no es correcta. Se permiten archivos de 3MB.";
        }

        return $resultado;
    }

    function validar_formato($nombre_docs,$formto_permitido){    
        $resultado="";
        global $_FILES;
      
        $tipo_archivo = $_FILES[$nombre_docs]['type'];
        $tipo_archivo=explode("/",$tipo_archivo);
        $tipo_archivo=strtolower($tipo_archivo[1]);

        $formto_permitido=strtolower($formto_permitido);
        
        if ($tipo_archivo !=  $formto_permitido) {
            $resultado.= "Formato no admitido, Se permiten archivos con formato .".$formto_permitido;
        }

        return $resultado;
    }


    function cargar_archivo($nombre_docs,$nombre_documento,$ruta_destino){
        $resultado="";
        global $_FILES;

        $tipo_archivo= $_FILES[$nombre_docs]['type'];
        $tipo_archivo=explode("/",$tipo_archivo);
        $tipo_archivo=$tipo_archivo[1];

        if(!is_dir($ruta_destino)){ 
            $resultado="No existe la ruta de destino" . $ruta_destino;
        }else{

            if (!move_uploaded_file($_FILES[$nombre_docs]['tmp_name'],  $ruta_destino."/".$nombre_documento.".".$tipo_archivo)){
                $resultado="Ocurrió algún error al subir el fichero. No pudo guardarse.";
            }
        }

        return $resultado;
    }
?>