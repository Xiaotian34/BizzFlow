<?php
class Estadisticas_Model {
    private $db;
    private $estadisticas;

    public function __construct() {
        require_once("model/conectar.php");
        $this->db = Conectar::conexion();
        $this->estadisticas = [];
    }

    public function get_estadistica_por_usuario($id_usuario) {
        $id_usuario = $this->db->real_escape_string($id_usuario);
        $sql = "SELECT * FROM estadisticas WHERE id_usuario = '$id_usuario' ORDER BY fecha_evento DESC";
        $consulta = $this->db->query($sql);
        while ($registro = $consulta->fetch_assoc()) {
            $this->estadisticas[] = $registro;
        }
        return $this->estadisticas;
    }

    public function registrar_estadistica($id_usuario, $tipo_estadistica, $descripcion) {
        $id_usuario = $this->db->real_escape_string($id_usuario);
        $tipo_estadistica = $this->db->real_escape_string($tipo_estadistica);
        $descripcion = $this->db->real_escape_string($descripcion);

        $sql = "INSERT INTO estadisticas (id_usuario, tipo_estadistica, descripcion)
                VALUES ('$id_usuario', '$tipo_estadistica', '$descripcion')";
        return $this->db->query($sql);
    }
}
?>
