<?php
class Facturas_Model {
    private $db;
    private $facturas;

    public function __construct() {
        require_once("model/conectar.php");
        $this->db = Conectar::conexion();
        $this->facturas = [];
    }

    public function get_facturas_por_usuario($id_usuario) {
        $id_usuario = $this->db->real_escape_string($id_usuario);
        $sql = "SELECT * FROM facturas WHERE id_usuario = '$id_usuario' ORDER BY fecha_emision DESC";
        $consulta = $this->db->query($sql);
        while ($registro = $consulta->fetch_assoc()) {
            $this->facturas[] = $registro;
        }
        return $this->facturas;
    }

    public function get_factura_por_id($id) {
        $id = $this->db->real_escape_string($id);
        $sql = "SELECT * FROM facturas WHERE id = '$id'";
        $consulta = $this->db->query($sql);
        return $consulta->fetch_assoc();
    }

    public function insertar_factura($id_usuario, $fecha_emision, $xml_generado, $total, $ruta_pdf) {
        $id_usuario = $this->db->real_escape_string($id_usuario);
        $fecha_emision = $this->db->real_escape_string($fecha_emision);
        $xml_generado = $this->db->real_escape_string($xml_generado);
        $total = $this->db->real_escape_string($total);
        $ruta_pdf = $this->db->real_escape_string($ruta_pdf);

        $sql = "INSERT INTO facturas (id_usuario, fecha_emision, xml_generado, total, ruta_pdf)
                VALUES ('$id_usuario', '$fecha_emision', '$xml_generado', '$total', '$ruta_pdf')";
        return $this->db->query($sql);
    }

    public function eliminar_factura($id) {
        $id = $this->db->real_escape_string($id);
        $sql = "DELETE FROM facturas WHERE id = '$id'";
        return $this->db->query($sql);
    }
}
?>
