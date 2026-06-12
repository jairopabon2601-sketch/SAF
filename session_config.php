<?php
// Incluir funciones de logging
include_once("logs/session_log.php");

// Configuración de sesión para evitar pérdida de sesión
ini_set('session.gc_maxlifetime', 3600); // 1 hora
ini_set('session.cookie_lifetime', 3600); // 1 hora
ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS

// Función para verificar si la sesión está activa
function verificar_sesion_activa() {
    if (!isset($_SESSION["codigo_origen"]) || !isset($_SESSION["codigo_usuario"])) {
        log_problema_sesion("Sesión inactiva - Variables de sesión faltantes");
        return false;
    }
    return true;
}

// Función para renovar la sesión
function renovar_sesion() {
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
        log_sesion("Sesión renovada para usuario: " . ($_SESSION["codigo_usuario"] ?? 'N/A'));
    }
}

// Función para validar acceso a formulario
function validar_acceso_formulario($codigo_formulario, $codigo_perfil) {
    if ($codigo_perfil == 6) {
        log_acceso_formulario($_SESSION["codigo_usuario"] ?? 'N/A', $codigo_formulario, true);
        return true; // Administrador tiene acceso a todo
    }
    
    if ($codigo_formulario <= 0) {
        return true; // No hay formulario específico
    }
    
    try {
        include_once("conexion/conexion.php");
        $conexion = new conexion_db();
        
        $sql = "SELECT COUNT(p.codigo) acceso
                FROM tbl_procesos_perfiles p
                WHERE p.codigo_perfil = " . $codigo_perfil . " 
                AND p.codigo_proceso = " . $codigo_formulario;
        
        $resultado = $conexion->ejecutar_sql($sql);
        $acceso = $resultado->fetch_array(MYSQLI_ASSOC);
        
        $acceso_permitido = $acceso['acceso'] > 0;
        log_acceso_formulario($_SESSION["codigo_usuario"] ?? 'N/A', $codigo_formulario, $acceso_permitido);
        
        return $acceso_permitido;
    } catch (Exception $e) {
        log_problema_sesion("Error al validar acceso: " . $e->getMessage());
        return false;
    }
}

// Función para manejar errores de sesión
function manejar_error_sesion($mensaje) {
    log_problema_sesion($mensaje);
    // Aquí puedes agregar lógica adicional como enviar notificaciones
}
?> 