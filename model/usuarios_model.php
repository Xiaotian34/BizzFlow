<?php
class Usuarios_Model {
    private $db;
    private $usuarios;

    public function __construct() {
        require_once("model/conectar.php");
        $this->db = Conectar::conexion();
        $this->usuarios = [];
    }

    public function get_usuarios() {
        $sql = "SELECT * FROM usuarios";
        $consulta = $this->db->query($sql);
        while ($registro = $consulta->fetch_assoc()) {
            $this->usuarios[] = $registro;
        }
        return $this->usuarios;
    }

    public function login($correo, $contra) {
        $correo = $this->limpiar($correo);
        $sql = "SELECT contrasena_hash FROM usuarios WHERE correo_electronico = '$correo'";
        $consulta = $this->db->query($sql);
        if ($fila = $consulta->fetch_assoc()) {
            return password_verify($contra, $fila['contrasena_hash']);
        }
        return false;
    }

    public function eliminarUsuario($correo) {
        $correo = $this->limpiar($correo);
        return $this->db->query("DELETE FROM usuarios WHERE correo_electronico = '$correo'");
    }

    public function insertar($nombre, $apellidos, $edad, $correo, $contra, $tipo) {
        $nombre     = $this->limpiar($nombre);
        $apellidos  = $this->limpiar($apellidos);
        $edad       = (int)$edad;
        $correo     = $this->limpiar($correo);
        $contra     = password_hash($contra, PASSWORD_DEFAULT);
        $tipo       = $this->limpiar($tipo);

        if ($this->obtenerIdUsuarioPorCorreo($correo)) return false;

        $sql = "INSERT INTO usuarios 
                (nombre, apellidos, edad, correo_electronico, contrasena_hash, tipo, fecha_registro, imagen_perfil)
                VALUES 
                ('$nombre', '$apellidos', $edad, '$correo', '$contra', '$tipo', NOW(), 'img/imgPerfil/defaultProfile.svg')";
        return $this->db->query($sql);
    }

    public function actualizarUsuario($correo_actual, $nombre, $apellidos, $edad, $correo_nuevo, $passwd = null) {
        $correo_actual  = $this->limpiar($correo_actual);
        $nombre         = $this->limpiar($nombre);
        $apellidos      = $this->limpiar($apellidos);
        $edad           = (int)$edad;
        $correo_nuevo   = $this->limpiar($correo_nuevo);

        $updatePass = '';
        if ($passwd) {
            $passwd_hashed = password_hash($passwd, PASSWORD_DEFAULT);
            $updatePass = ", contrasena_hash = '$passwd_hashed'";
        }

        $sql = "UPDATE usuarios SET 
                nombre = '$nombre', apellidos = '$apellidos', edad = $edad, correo_electronico = '$correo_nuevo' $updatePass
                WHERE correo_electronico = '$correo_actual'";

        return $this->db->query($sql);
    }

    public function cambiarContrasena($correo, $nueva_contra) {
        $correo = $this->limpiar($correo);
        $hash = password_hash($nueva_contra, PASSWORD_DEFAULT);
        return $this->db->query("UPDATE usuarios SET contrasena_hash = '$hash' WHERE correo_electronico = '$correo'");
    }

    public function registroGoogle($nombre, $correo, $contra, $google_id = null) {
        $nombre     = $this->limpiar($nombre);
        $correo     = $this->limpiar($correo);
        $contra     = $contra ? password_hash($contra, PASSWORD_DEFAULT) : null;
        $google_id  = $google_id ? $this->limpiar($google_id) : null;
        $tipo       = 'usuario';
        $img        = 'img/imgPerfil/defaultProfile.svg';

        $sql = "INSERT INTO usuarios 
                (nombre, apellidos, edad, correo_electronico, contrasena_hash, google_id, tipo, fecha_registro, imagen_perfil)
                VALUES ('$nombre', '', 0, '$correo', " . 
                ($contra ? "'$contra'" : "NULL") . ", " . 
                ($google_id ? "'$google_id'" : "NULL") . ", '$tipo', NOW(), '$img')";

        return $this->db->query($sql);
    }

    public function obtenerNombreUsuarioPorCorreo($correo) {
        return $this->fetchFieldByCorreo($correo, 'nombre');
    }

    public function obtenerIdUsuarioPorCorreo($correo) {
        return $this->fetchFieldByCorreo($correo, 'id');
    }

    public function obtenerTipoUsuario($correo) {
        return $this->fetchFieldByCorreo($correo, 'tipo');
    }

    public function actualizarImagenPerfil($correo, $ruta_imagen) {
        $correo = $this->limpiar($correo);
        $ruta_imagen = $this->limpiar($ruta_imagen);
        return $this->db->query("UPDATE usuarios SET imagen_perfil = '$ruta_imagen' WHERE correo_electronico = '$correo'");
    }

    public function obtenerImagenPerfil($correo) {
        $img = $this->fetchFieldByCorreo($correo, 'imagen_perfil');
        return $img ?: 'img/imgPerfil/defaultProfile.svg';
    }

    // === MÉTODOS PRIVADOS AUXILIARES ===

    private function limpiar($valor) {
        return $this->db->real_escape_string($valor);
    }

    private function fetchFieldByCorreo($correo, $campo) {
        $correo = $this->limpiar($correo);
        $sql = "SELECT $campo FROM usuarios WHERE correo_electronico = '$correo'";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            $fila = $result->fetch_assoc();
            return $fila[$campo];
        }
        return null;
    }
}
?>
