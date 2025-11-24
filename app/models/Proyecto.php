<?php
require_once '../app/core/Database.php';

class Proyecto {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    public function getAll($estado = null) {
        $query = "  SELECT p.*, u.nombre_completo as creador_nombre 
                    FROM proyectos p 
                    JOIN usuarios u ON p.usuario_creador_id = u.id_usuario";
        
        if ($estado) {
            $query .= " WHERE p.estado = :estado";
        }
        
        $query .= " ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($estado) {
            $stmt->bindParam(':estado', $estado);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getActive($limit = 5) {
        $query = "  SELECT p.*, u.nombre_completo as creador_nombre 
                    FROM proyectos p 
                    JOIN usuarios u ON p.usuario_creador_id = u.id_usuario 
                    WHERE p.estado = 'vigente'
                    ORDER BY p.fecha_creacion DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($nombre, $descripcion, $usuario_id, $fecha_inicio) {
        $query = "  INSERT INTO proyectos (nombre, descripcion, usuario_creador_id, fecha_inicio) 
                    VALUES (:nombre, :descripcion, :usuario_id, :fecha_inicio)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        
        return $stmt->execute();
    }
    
    public function followProject($proyecto_id, $usuario_id) {
        $query = "INSERT INTO proyecto_seguidores (proyecto_id, usuario_id) VALUES (:proyecto_id, :usuario_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':proyecto_id', $proyecto_id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }
    public function getByUser($usuario_id) {
        $query = "  SELECT p.*, u.nombre_completo as creador_nombre
                    FROM proyectos p
                    JOIN usuarios u ON p.usuario_creador_id = u.id_usuario
                    WHERE p.usuario_creador_id = :usuario_id
                    ORDER BY p.fecha_creacion DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener proyectos por usuario: " . $e->getMessage());
        }
    }

    public function getFollowedByUser($usuario_id) {
        $query = "  SELECT p.*, u.nombre_completo as creador_nombre
                    FROM proyectos p
                    JOIN usuarios u ON p.usuario_creador_id = u.id_usuario
                    JOIN proyecto_seguidores ps ON p.id_proyecto = ps.proyecto_id
                    WHERE ps.usuario_id = :usuario_id
                    ORDER BY p.fecha_creacion DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener proyectos seguidos: " . $e->getMessage());
        }
    }
}
?>