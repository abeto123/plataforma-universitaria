<?php
require_once '../app/core/Database.php';

class Idea {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    public function getAll() {
        $query = "  SELECT i.*, u.nombre_completo as creador_nombre 
                    FROM ideas i 
                    JOIN usuarios u ON i.usuario_creador_id = u.id_usuario 
                    ORDER BY i.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLatest($limit = 5) {
        $query = "  SELECT i.*, u.nombre_completo as creador_nombre 
                    FROM ideas i 
                    JOIN usuarios u ON i.usuario_creador_id = u.id_usuario 
                    ORDER BY i.fecha_creacion DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($titulo, $descripcion, $usuario_id) {
        $query = "  INSERT INTO ideas (titulo, descripcion, usuario_creador_id) 
                    VALUES (:titulo, :descripcion, :usuario_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }
    
    public function joinIdea($idea_id, $usuario_id) {
        $query = "INSERT INTO idea_miembros (idea_id, usuario_id) VALUES (:idea_id, :usuario_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':idea_id', $idea_id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }
    public function getByUser($usuario_id) {
        $query = "  SELECT i.*, u.nombre_completo as creador_nombre
                    FROM ideas i
                    JOIN usuarios u ON i.usuario_creador_id = u.id_usuario
                    WHERE i.usuario_creador_id = :usuario_id
                    ORDER BY i.fecha_creacion DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener ideas por usuario: " . $e->getMessage());
        }
    }
}
?>