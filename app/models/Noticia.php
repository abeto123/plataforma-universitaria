<?php
class Noticia {
    private $db;
    public function __construct(){ $this->db = Database::connect(); }

    public function obtenerRecientes($limite = 3){
        $sql = "SELECT * FROM noticias ORDER BY fecha_publicacion DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function actualizar($id, $titulo, $contenido, $tipo){
        $sql = "UPDATE noticias SET titulo = :tit, contenido = :cont, tipo = :tip WHERE id_noticia = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tit'=>$titulo, ':cont'=>$contenido, ':tip'=>$tipo, ':id'=>$id]);
        return true;
    }

    public function eliminar($id){
        $sql = "DELETE FROM noticias WHERE id_noticia = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    }

        // 1. Listar noticias (con filtro opcional por tipo)
    public function obtenerTodas($tipo = null){
        $sql = "SELECT n.*, u.nombre_completo as autor
                FROM noticias n
                JOIN usuarios u ON n.usuario_publicador_id = u.id_usuario
                WHERE 1=1";
        
        if($tipo){
            $sql .= " AND n.tipo = :tipo";
        }

        $sql .= " ORDER BY n.fecha_publicacion DESC";

        $stmt = $this->db->prepare($sql);
        if($tipo) { $stmt->bindValue(':tipo', $tipo); }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 2. Ver una noticia individual
    public function obtenerPorId($id){
        $sql = "SELECT n.*, u.nombre_completo as autor
                FROM noticias n
                JOIN usuarios u ON n.usuario_publicador_id = u.id_usuario
                WHERE n.id_noticia = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // 3. Crear Noticia (Solo admin)
    public function crear($datos){
        $sql = "INSERT INTO noticias (titulo, contenido, tipo, usuario_publicador_id) 
                VALUES (:tit, :cont, :tip, :uid)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tit', $datos['titulo']);
        $stmt->bindValue(':cont', $datos['contenido']);
        $stmt->bindValue(':tip', $datos['tipo']);
        $stmt->bindValue(':uid', $datos['usuario_id']);
        return $stmt->execute();
    }
}