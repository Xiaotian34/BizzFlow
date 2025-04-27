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
        $contra = $this->db->real_escape_string($contra);

        $sql = "SELECT * FROM usuarios WHERE correo_electronico = '$correo'";
        $consulta = $this->db->query($sql);
        if ($fila = $consulta->fetch_assoc()) {
            return $fila['contrasena_hash'] === $contra; // O usa password_verify($contra, $fila['contrasena_hash'])
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
        $contra = $this->db->real_escape_string($contra); // O usa password_hash()
        $tipo = $this->db->real_escape_string($tipo);

        $sql = "INSERT INTO usuarios (nombre, correo_electronico, contrasena_hash, tipo) 
                VALUES ('$nombre', '$correo', '$contra', '$tipo')";
        return $this->db->query($sql);
    }
}
?>