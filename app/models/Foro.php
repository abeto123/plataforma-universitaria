<?php
require_once '../app/core/Database.php';

class Foro {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    public function getAllPublicaciones() {
        $query = "  SELECT fp.*, u.nombre_completo as usuario_nombre, fc.nombre as categoria_nombre
                    FROM foro_publicaciones fp 
                    JOIN usuarios u ON fp.usuario_id = u.id_usuario 
                    LEFT JOIN foro_categorias fc ON fp.categoria_id = fc.id_categoria
                    ORDER BY fp.fecha_publicacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategorias() {
        $query = "SELECT * FROM foro_categorias ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createPublicacion($titulo, $contenido, $usuario_id, $categoria_id) {
        $query = "INSERT INTO foro_publicaciones (titulo, contenido, usuario_id, categoria_id) 
                VALUES (:titulo, :contenido, :usuario_id, :categoria_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':categoria_id', $categoria_id);
        
        return $stmt->execute();
    }
    
    public function getPublicacion($id) {
        $query = "  SELECT fp.*, u.nombre_completo as usuario_nombre, fc.nombre as categoria_nombre
                    FROM foro_publicaciones fp 
                    JOIN usuarios u ON fp.usuario_id = u.id_usuario 
                    LEFT JOIN foro_categorias fc ON fp.categoria_id = fc.id_categoria
                    WHERE fp.id_foro_publicacion = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getRespuestas($publicacion_id) {
        $query = "  SELECT fr.*, u.nombre_completo as usuario_nombre
                    FROM foro_respuestas fr 
                    JOIN usuarios u ON fr.usuario_id = u.id_usuario 
                    WHERE fr.publicacion_id = :publicacion_id
                    ORDER BY fr.fecha_respuesta ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':publicacion_id', $publicacion_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createRespuesta($contenido, $publicacion_id, $usuario_id) {
        $query = "  INSERT INTO foro_respuestas (contenido, publicacion_id, usuario_id) 
                    VALUES (:contenido, :publicacion_id, :usuario_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':publicacion_id', $publicacion_id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }
}
?>