<?php
	
	error_reporting(E_ERROR);


	class conexion_db {


		private $host="s19ff36e.alojamientovirtual.com";
    	private $usuario="s19ff36e_usaf";
    	private $clave="Saf2020!";
    	private $db="s19ff36e_saf";
		private $conexion;

		function __construct(){

			
			$this->conexion = new mysqli($this->host, $this->usuario, $this->clave,$this->db);

	
	        if($this->conexion->connect_errno){
	        	echo "Fallo al conectar a MySQL: " .$this->conexion->connect_error;
	  
	        }else{
	        	$this->conexion->set_charset("utf8");
	        	$this->conexion->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
	        	
	        }

		}

		public function ejecutar_sql($sql){
			
	        $resultado = $this->conexion->query($sql) or die($this->conexion->error);

	        if($resultado)
	            return $resultado;
	        return false;
    	} 

		public function buscar($tabla, $condicion){
		
			if ($condicion=="") {
				$condicion=1;
			}

	        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion") or die($this->conexion->error);
	        if($resultado)
	            return $resultado->fetch_all(MYSQLI_ASSOC);
	        return false;
    	} 

    	public function insertar($tabla, $datos){


    		$campos="";
    		$valores="";

    		$i=0;
    		foreach ($datos as $key => $value) {

    			$coma= $i<count($datos)-1 ? ',' : '';
    			
    			$campos.=$key;
    			$campos.=$coma ;

    			$valores.="'" . $value ."'";
    			$valores.=$coma;

    			$i++;
    		}


    		$sql="INSERT INTO " . $tabla ;
    		$sql.="(".$campos.")";
    		$sql.=" VALUES (".$valores.")";


        	$resultado =    $this->conexion->query($sql);
        	if($resultado)
				return true;
			return false;
    	} 

    	public function fetch_all(){
    		return $this->conexion->fetch_all(MYSQLI_ASSOC);
    	}

    	public function error(){
    		return $this->conexion->error;
    	}

    	public function fetch_array(){
    		return $this->conexion->fetch_array(MYSQLI_NUM);
    	}
    
    	public function insert_id(){
    		return $this->conexion->insert_id;
    	}

    	public function num_rows(){
    		return $this->conexion->num_rows;
    	}

    	public function actualizar($tabla, $datos, $condicion){
			
    		if ($condicion == "" || $condicion === null || $condicion === "undefined") {
    			return "Debe agregar una condición.";   	
    		}else{ 

    			$campos="";
    			$valores="";

    			$i=0;
	    		foreach ($datos as $key => $value) {

	    			$coma= $i<count($datos)-1 ? ',' : '';
	    			
	    			$campos.=$key."='".$value."'";
	    			$campos.=$coma ;

	    			$i++;
	    		}

	    		$sql="UPDATE $tabla SET $campos WHERE $condicion";

		        $resultado  =   $this->conexion->query($sql);
		        if($resultado)
		            return true;
		        return false;   
		    }     
    	} 

    	public function borrar($tabla, $condicion){ 

    		if ($condicion == "" || $condicion === null || $condicion === "undefined") {
    			return "Debe agregar una condición.";   	
    		}else{
	        	$resultado  =   $this->conexion->query("DELETE FROM $tabla WHERE $condicion") or die($this->conexion->error);
	        	if($resultado)
	            	return true;
	        	return false;
    		}

    	}

    	public function listado_select($tabla,$valor,$etiqueta,$filtro,$campo_orden){
    		
    		$filtro= $filtro=='' ? '1' : $filtro;
			$filtro =  str_replace("\\", "", $filtro);

			$sql="select ".$valor.", ".$etiqueta." from ".$tabla." where ".$filtro;
			
			if ($campo_orden != "" && $campo_orden !== null && $campo_orden !== "undefined") {
				$sql.=" order by " . $campo_orden;	
			}

			$resultado=$this->conexion->query($sql);

			if($resultado->num_rows > 0){
				$num=0;
				while($row = $resultado->fetch_array(MYSQLI_NUM)){
				
					$data[$num][$valor] = $row[0];
					$data[$num][$etiqueta] = $row[1];
					$num++;
				}
			}else{
				$data = '0';
			}

			return $data;
    	}

    	public function consultar_campo($tabla,$nombre_campo,$filtro){
    		$data="";

			$filtro = $filtro=='' ? '1' : $filtro; 
			$filtro =  str_replace("\\", "", $filtro);
			//BUSCAMOS LAS COLUMNAS DE LA TABLA.
			$sql = "SELECT ".$nombre_campo." FROM ".$tabla." WHERE ".$filtro;


			$resultado=$this->conexion->query($sql);
			
			if($resultado->num_rows > 0){
				$data=$resultado->fetch_array(MYSQLI_NUM);
			}else{
				$data=0;				
			}

			return $data;
    	}

	}

?>