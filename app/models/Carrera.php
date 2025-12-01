<?php
class Carrera {
    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    // Obtener lista completa de carreras ordenadas alfabéticamente
    public function obtenerTodas(){
        $sql = "SELECT * FROM carreras ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>