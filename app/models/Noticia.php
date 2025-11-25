<?php

require_once '../app/core/Database.php';

class Noticia {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    public function getAll($tipo = null) {
        $query = "  SELECT n.*, u.nombre_completo as publicador_nombre 
                    FROM noticias n 
                    JOIN usuarios u ON n.usuario_publicador_id = u.id_usuario";
        
        if ($tipo) {
            $query .= " WHERE n.tipo = :tipo";
        }
        
        $query .= " ORDER BY n.fecha_publicacion DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($tipo) {
            $stmt->bindParam(':tipo', $tipo);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLatest($limit = 5) {
        $query = "  SELECT n.*, u.nombre_completo as publicador_nombre 
                    FROM noticias n 
                    JOIN usuarios u ON n.usuario_publicador_id = u.id_usuario 
                    ORDER BY n.fecha_publicacion DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($titulo, $contenido, $tipo, $usuario_id) {
        $query = "  INSERT INTO noticias (titulo, contenido, tipo, usuario_publicador_id) 
                    VALUES (:titulo, :contenido, :tipo, :usuario_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }
}
?>