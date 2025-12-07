<?php
class Foro {
    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    public function actualizarPregunta($id, $titulo, $contenido){
        $sql = "UPDATE foro_publicaciones SET titulo = :tit, contenido = :cont WHERE id_foro_publicacion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tit' => $titulo, ':cont' => $contenido, ':id' => $id]);
        return true;
    }

    public function eliminarPregunta($id){
        $sql = "DELETE FROM foro_publicaciones WHERE id_foro_publicacion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    }

    // 1. Obtener todas las categorías
    public function obtenerCategorias(){
        $stmt = $this->db->query("SELECT * FROM foro_categorias ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 2. Listar preguntas (Con filtro opcional)
    public function obtenerPreguntas($categoria_id = null, $busqueda = null){
        $sql = "SELECT p.*, u.nombre_completo as autor, c.nombre as categoria,
                (SELECT COUNT(*) FROM foro_respuestas r WHERE r.publicacion_id = p.id_foro_publicacion) as num_respuestas
                FROM foro_publicaciones p
                JOIN usuarios u ON p.usuario_id = u.id_usuario
                LEFT JOIN foro_categorias c ON p.categoria_id = c.id_categoria
                WHERE 1=1";

        if($categoria_id){
            $sql .= " AND p.categoria_id = :cat";
        }
        if($busqueda){
            $sql .= " AND (p.titulo LIKE :busq OR p.contenido LIKE :busq)";
        }

        $sql .= " ORDER BY p.fecha_publicacion DESC";

        $stmt = $this->db->prepare($sql);
        if($categoria_id) { $stmt->bindValue(':cat', $categoria_id); }
        if($busqueda) { $stmt->bindValue(':busq', "%$busqueda%"); }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 3. Ver detalle de una pregunta
    public function obtenerPreguntaPorId($id){
        $sql = "SELECT p.*, u.nombre_completo as autor, c.nombre as categoria
                FROM foro_publicaciones p
                JOIN usuarios u ON p.usuario_id = u.id_usuario
                LEFT JOIN foro_categorias c ON p.categoria_id = c.id_categoria
                WHERE p.id_foro_publicacion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // 4. Obtener respuestas de una pregunta
    public function obtenerRespuestas($id_pregunta){
        $sql = "SELECT r.*, u.nombre_completo as autor, u.rol
                FROM foro_respuestas r
                JOIN usuarios u ON r.usuario_id = u.id_usuario
                WHERE r.publicacion_id = :pid
                ORDER BY r.fecha_respuesta ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pid', $id_pregunta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 5. Crear Pregunta
    public function crearPregunta($datos){
        $sql = "INSERT INTO foro_publicaciones (titulo, contenido, usuario_id, categoria_id) 
                VALUES (:tit, :cont, :uid, :cat)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tit', $datos['titulo']);
        $stmt->bindValue(':cont', $datos['contenido']);
        $stmt->bindValue(':uid', $datos['usuario_id']);
        $stmt->bindValue(':cat', $datos['categoria_id']);
        return $stmt->execute();
    }

    // 6. Responder
    public function responder($datos){
        $sql = "INSERT INTO foro_respuestas (contenido, publicacion_id, usuario_id) 
                VALUES (:cont, :pid, :uid)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cont', $datos['contenido']);
        $stmt->bindValue(':pid', $datos['publicacion_id']);
        $stmt->bindValue(':uid', $datos['usuario_id']);
        return $stmt->execute();
    }
}