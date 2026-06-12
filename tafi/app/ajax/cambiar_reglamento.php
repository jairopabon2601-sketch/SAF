<?php        

    $nombre_archivo = $_FILES['reglamento']['name'];
    $tipo_archivo = $_FILES['reglamento']['type'];
    $tamano_archivo = $_FILES['reglamento']['size'];
    $codigo_torneo=$_POST['codigo_torneo'];

    $resultado="";

    if(isset($_POST['codigo_torneo'])){

        if (!((strpos($tipo_archivo, "pdf")) && ($tamano_archivo < 300000))) {
            $resultado.= "La extensión o el tamaño de los archivos no es correcta. Se permiten archivos .pdf <br><li>se permiten archivos de 3MB.";
        }else{
            if (move_uploaded_file($_FILES['reglamento']['tmp_name'],  "../reglamentos/reglamento_".$codigo_torneo.".pdf")){
                //SE ACTUALIZA EL REGISTRO
                $resultado.="Documento Actualizado";
            }else{
                $resultado.="Ocurrió algún error al subir el fichero. No pudo guardarse.";
            }
        }
    }else{
        $resultado.="No se ha seleccionado un torneo";
    }

    echo "<script>alert('".$resultado."'); window.location.href='../dashboard.php?proc=5&codigo_torneo=".$codigo_torneo."';</script>";

?>