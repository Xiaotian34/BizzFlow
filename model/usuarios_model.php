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
        $correo = $this->db->real_escape_string($correo);

        $sql = "SELECT * FROM usuarios WHERE correo_electronico = '$correo'";
        $consulta = $this->db->query($sql);
        if ($fila = $consulta->fetch_assoc()) {
            return password_verify($contra, $fila['contrasena_hash']); // Verifica el hash
        }
        return false;
    }

    public function eliminarUsuario($correo) {
        $correo = $this->db->real_escape_string($correo);
        $sql = "DELETE FROM usuarios WHERE correo_electronico = '$correo'";
        return $this->db->query($sql);
    }

    public function insertar($nombre, $correo, $contra, $tipo = 'usuario') {
        $nombre = $this->db->real_escape_string($nombre);
        $correo = $this->db->real_escape_string($correo);
        $contra = password_hash($contra, PASSWORD_DEFAULT); // Aplica hash a la contraseña
        $tipo = $this->db->real_escape_string($tipo);

        $sql = "INSERT INTO usuarios (nombre, correo_electronico, contrasena_hash, tipo) 
                VALUES ('$nombre', '$correo', '$contra', '$tipo')";
        return $this->db->query($sql);
    }

    public function actualizarUsuario($correo_actual, $nombre, $correo_nuevo, $passwd) {
        $correo_actual = $this->db->real_escape_string($correo_actual);
        $nombre = $this->db->real_escape_string($nombre);
        $correo_nuevo = $this->db->real_escape_string($correo_nuevo);
        
        // Update user information
        $sql = "UPDATE usuarios SET nombre = '$nombre', correo_electronico = '$correo_nuevo' WHERE correo_electronico = '$correo_actual'";
        return $this->db->query($sql);
    }
    
    public function cambiarContrasena($correo, $nueva_contra) {
        $correo = $this->db->real_escape_string($correo);
        $nueva_contra = password_hash($nueva_contra, PASSWORD_DEFAULT); // Hash the new password
    
        // Update password
        $sql = "UPDATE usuarios SET contrasena_hash = '$nueva_contra' WHERE correo_electronico = '$correo'";
        return $this->db->query($sql);
    }

    public function registroGoogle($nombre, $correo, $contra, $google_id = null) {
        $nombre = $this->db->real_escape_string($nombre);
        $correo = $this->db->real_escape_string($correo);
        $contra = $contra ? password_hash($contra, PASSWORD_DEFAULT) : null; // Hash de la contraseña si existe
        $google_id = $google_id ? $this->db->real_escape_string($google_id) : null;
    
        $sql = "INSERT INTO usuarios (nombre, correo_electronico, contrasena_hash, google_id) 
                VALUES ('$nombre', '$correo', " . ($contra ? "'$contra'" : "NULL") . ", " . ($google_id ? "'$google_id'" : "NULL") . ")";
        return $this->db->query($sql);
    }

    public function obtenerNombreUsuarioPorCorreo($correo) {
        $correo = $this->db->real_escape_string($correo);
        $sql = "SELECT nombre FROM usuarios WHERE correo_electronico = '$correo'";
        $result = $this->db->query($sql);
    
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['nombre'];
        }
        return null;
    }
    
    public function obtenerIdUsuarioPorCorreo($correo) {
        $correo = $this->db->real_escape_string($correo);
        $sql = "SELECT id FROM usuarios WHERE correo_electronico = '$correo'";
        $result = $this->db->query($sql);
    
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        return null;
    }
}
?>