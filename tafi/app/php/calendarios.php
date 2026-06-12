<?php
    session_start();
	require_once($_SERVER['DOCUMENT_ROOT']."/tafi/conexion/conexion.php");
	
	$conexion=new conexion_db();

    class calendario {
        
		protected $conexion;

		public function __construct() {

        	$this->conexion =new conexion_db();
    	}

        public function CargarCalendario($codigo_torneo,$codigo_clasificacion,$codigo_ronda){

            $grupos=array();
            $fechas=array();

            //SE CARGAR LOS GRUPOS DEL TORNEO
            $sql="SELECT 

            grup.codigo_grupo, 
            grup.nombre_grupo,
            eq.nombre_equipo,
            eq.escudo
            
            
            FROM tbl_tafi_torneos_calendario_fechas_grupos grup 
            
            INNER JOIN tbl_tafi_torneos_calendario_fechas_grupos_equipos ge ON 
            grup.codigo_grupo=ge.codigo_grupo
            AND grup.codigo_torneo=ge.codigo_torneo
            
            INNER JOIN tbl_tafi_equipos eq ON 
            ge.codigo_equipo=eq.codigo_equipo
            
            WHERE grup.codigo_torneo='".$codigo_torneo."'
            
            GROUP BY ge.codigo
            
            ORDER BY grup.nombre_grupo";

            $resultado=$this->conexion->ejecutar_sql($sql);
            $datos=$resultado->fetch_all(MYSQLI_ASSOC);
            $grupos=$datos; 

            if($codigo_clasificacion==1){
                //TODOS CONTRA TODOS 
                $sql='SELECT 

                cal.codigo_calendario,
                f.codigo_fecha,
                f.nombre_fecha,
                elocal.codigo_equipo AS "cod_eq_local",
                elocal.nombre_equipo AS "nom_eq_local",
                elocal.escudo AS "esq_eq_local",
                evisitante.codigo_equipo AS "cod_eq_visitante",
                evisitante.nombre_equipo AS "nom_eq_visitante",
                evisitante.escudo AS "esq_eq_visitante",
                cal.fecha, 
                cal.hora, 
                cal.codigo_sede,
                esta.permite_modificacion
               
                
                FROM tbl_tafi_torneos_calendario_fechas f 
                
                INNER JOIN tbl_tafi_torneos_calendario cal ON 
                f.codigo_fecha=cal.codigo_fecha

                INNER JOIN tbl_tafi_torneos_calendario_estados esta on 
                cal.codigo_estado=esta.codigo_estado
                
                INNER JOIN tbl_tafi_equipos elocal ON 
                cal.codigo_local=elocal.codigo_equipo
                
                INNER JOIN tbl_tafi_equipos evisitante ON 
                cal.codigo_visitante=evisitante.codigo_equipo
                
                WHERE f.codigo_torneo="'.$codigo_torneo.'"
                and f.codigo_ronda="'.$codigo_ronda.'"
                
                GROUP BY cal.codigo_calendario
                
                ORDER BY f.numero, cal.codigo_calendario';

                $resultado_f=$this->conexion->ejecutar_sql($sql);
                
                $datos_fechas=$resultado_f->fetch_all(MYSQLI_ASSOC);
                $fechas=$datos_fechas;
            }else{
                //FASE DE GRUPOS
                $sql='SELECT 

                cal.codigo_calendario,
                grup.codigo_grupo, 
                grup.nombre_grupo,
                f.codigo_fecha,
                f.nombre_fecha,
                elocal.codigo_equipo AS "cod_eq_local",
                elocal.nombre_equipo AS "nom_eq_local",
                elocal.escudo AS "esq_eq_local",
                evisitante.codigo_equipo AS "cod_eq_visitante",
                evisitante.nombre_equipo AS "nom_eq_visitante",
                evisitante.escudo AS "esq_eq_visitante",
                cal.fecha, 
                cal.hora, 
                cal.codigo_sede,
                esta.permite_modificacion

                
                FROM tbl_tafi_torneos_calendario_fechas_grupos grup 
                
                INNER JOIN tbl_tafi_torneos_calendario_fechas_grupos_equipos ge ON 
                grup.codigo_grupo=ge.codigo_grupo
                AND grup.codigo_torneo=ge.codigo_torneo
                
                INNER JOIN tbl_tafi_torneos_calendario_fechas f ON
                grup.codigo_grupo=f.codigo_grupo
                AND grup.codigo_torneo=f.codigo_torneo
                
                INNER JOIN tbl_tafi_torneos_calendario cal ON 
                cal.codigo_fecha=f.codigo_fecha
                AND cal.codigo_grupo=grup.codigo_grupo

                INNER JOIN tbl_tafi_torneos_calendario_estados esta on 
                cal.codigo_estado=esta.codigo_estado
                
                INNER JOIN tbl_tafi_equipos elocal ON 
                cal.codigo_local=elocal.codigo_equipo
                
                INNER JOIN tbl_tafi_equipos evisitante ON 
                cal.codigo_visitante=evisitante.codigo_equipo
                
                WHERE grup.codigo_torneo="'.$codigo_torneo.'"
                AND f.codigo_ronda="'.$codigo_ronda.'"
                
                GROUP BY cal.codigo_calendario
                
                ORDER BY f.numero, cal.codigo_calendario';

                $resultado_f=$this->conexion->ejecutar_sql($sql);
                
                $datos_fechas=$resultado_f->fetch_all(MYSQLI_ASSOC);
                $fechas=$datos_fechas;
            }

            
            $retorno["grupos"]=$grupos;
            $retorno["fechas"]=$datos_fechas;

            return $retorno;
        }

        public function faseGrupo($equipos, $equiposGrupos,$cantidad_rondas){
            $retorno=array();
            shuffle($equipos);

            $grupos = $this->array_chunk_fixed($equipos,$equiposGrupos,false);

            //PARTIDOS 
            $partidos=array();
            foreach ($grupos as $key => $value) {
                $partidos[$key]=$this->todosContraTodos($value, $cantidad_rondas);
            }

            $retorno["grupos"]=$grupos;
            $retorno["partidos"]=$partidos;

            return $retorno;
        }


        public function todosContraTodos($names, $rondas){
            shuffle($names);
            
            $teams = sizeof($names);

            $totalRounds = $teams - 1;
            $matchesPerRound = $teams / 2;
            $rounds = array();
            for ($i = 0; $i < $totalRounds; $i++) {
                $rounds[$i] = array();
            }
            
        
            $sw=0;
            for ($round = 0; $round < $totalRounds; $round++) {
                for ($match = 0; $match < $matchesPerRound; $match++) {
                    $home = ($round + $match) % ($teams - 1);
                    $away = ($teams - 1 - $match + $round) % ($teams - 1);
                    // Last team stays in the same place while the others
                    // rotate around it.
                    if ($match == 0) {
                        $away = $teams - 1;
                    }

                    
                    $rounds[$round][$match] = $this->team_name($home + 1, $names) 
                        . "vs" . $this->team_name($away + 1, $names);

                    
                }
                $sw++;
            }

            // Interleave so that home and away games are fairly evenly dispersed.
            $interleaved = array();
            for ($i = 0; $i < $totalRounds; $i++) {
                $interleaved[$i] = array();
            }
            
            $evn = 0;
            $odd = ($teams / 2);
            for ($i = 0; $i < sizeof($rounds); $i++) {
                if ($i % 2 == 0) {
                    $interleaved[$i] = $rounds[$evn++];
                } else {
                    $interleaved[$i] = $rounds[$odd++];
                }
            }

            $rounds = $interleaved;

       
            if( $rondas==2){
                $round2=$rounds;

                foreach ($round2 as $key => $value) {
                    foreach ($value as $key2 => $value2) {
            
                        $round2[$key][$key2]=$this->flip($value2);
                        
                    }
                }

                $rounds = array_merge($rounds, array_reverse($round2));
            }
            
            return $rounds;
        }

        private function flip($match) {
            $components = explode('vs', $match);
            return $components[1] . "vs" . $components[0];
        }

        private function team_name($num, $names) {
            $i = $num - 1;
            if (sizeof($names) > $i && strlen(trim($names[$i])) > 0) {
                return trim($names[$i]);
            } else {
                return $num;
            }
        }

        private function array_chunk_fixed($input, $num, $preserve_keys = false) {
            
        
            $count = count($input);
            if ($count <= 0)
                return false;
            if ($num <= 0)
                return false;
            $out = array();
            $idx = 0;

            $ceil = ceil($count / $num);

            for ($j = 0; $j < $ceil; $j++) {
                $out[$j] = array();
                for ($i = 0; $i < $num; $i++) {
                    if ($idx < $count) {
                        if ($preserve_keys)
                            $out[$j][$idx] = $input[$idx];
                        else
                            $out[$j][$i] = $input[$idx];
                        $idx++;
                    }
                }
            }

            return $out;
        }

    }

?>