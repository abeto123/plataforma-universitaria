<?php
class Idea {
    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    public function obtenerRecientes($limite = 3){
        $sql = "SELECT i.id_idea, i.titulo, i.descripcion, u.nombre_completo as autor 
                FROM ideas i 
                JOIN usuarios u ON i.usuario_creador_id = u.id_usuario 
                ORDER BY i.fecha_creacion DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 1. Listar todas (Para el index)
    public function obtenerTodas(){
        $sql = "SELECT i.*, u.nombre_completo as autor, c.nombre as carrera
                FROM ideas i
                JOIN usuarios u ON i.usuario_creador_id = u.id_usuario
                LEFT JOIN carreras c ON u.carrera_id = c.id_carrera
                ORDER BY i.fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 2. Obtener UNA sola idea por ID (Para el detalle)
    public function obtenerPorId($id){
        $sql = "SELECT i.*, u.nombre_completo as autor, u.correo_electronico, c.nombre as carrera
                FROM ideas i
                JOIN usuarios u ON i.usuario_creador_id = u.id_usuario
                LEFT JOIN carreras c ON u.carrera_id = c.id_carrera
                WHERE i.id_idea = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function obtenerMiembros($id_idea){
        // Agregamos u.telefono a la consulta
        $sql = "SELECT u.id_usuario, u.nombre_completo, u.telefono, c.nombre as carrera
                FROM idea_miembros im
                JOIN usuarios u ON im.usuario_id = u.id_usuario
                LEFT JOIN carreras c ON u.carrera_id = c.id_carrera
                WHERE im.idea_id = :id AND im.estado_solicitud = 'aceptado'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_idea);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 4. Crear nueva idea
    public function crear($datos){
        $sql = "INSERT INTO ideas (titulo, descripcion, usuario_creador_id) VALUES (:titulo, :desc, :uid)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':titulo', $datos['titulo']);
        $stmt->bindValue(':desc', $datos['descripcion']);
        $stmt->bindValue(':uid', $datos['usuario_id']);
        return $stmt->execute();
    }

    // 5. Unirse a una idea
    public function unirse($id_idea, $id_usuario, $mensaje = ''){
        if($this->verificarMembresia($id_idea, $id_usuario)){ return false; }
        
        $sql = "INSERT INTO idea_miembros (idea_id, usuario_id, mensaje_solicitud, estado_solicitud) 
                VALUES (:idea, :user, :msg, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':idea', $id_idea);
        $stmt->bindValue(':user', $id_usuario);
        $stmt->bindValue(':msg', $mensaje);
        return $stmt->execute();
    }

    // 6. Verificar si ya es miembro
    public function verificarMembresia($id_idea, $id_usuario){
        $sql = "SELECT * FROM idea_miembros WHERE idea_id = :idea AND usuario_id = :user";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':idea', $id_idea);
        $stmt->bindValue(':user', $id_usuario);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function obtenerRecomendadas($carrera_usuario_id){
        // Busca ideas donde el creador NO sea de tu misma carrera (fomenta interdisciplinariedad)
        // O busca ideas que explícitamente pidan tu carrera (si tuvieras esa tabla)
        
        // Ejemplo simple: Mostrar ideas de otras carreras para fomentar colaboración
        $sql = "SELECT i.*, u.nombre_completo as autor, c.nombre as carrera
                FROM ideas i
                JOIN usuarios u ON i.usuario_creador_id = u.id_usuario
                JOIN carreras c ON u.carrera_id = c.id_carrera
                WHERE u.carrera_id != :mi_carrera
                ORDER BY i.fecha_creacion DESC LIMIT 3";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mi_carrera', $carrera_usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function obtenerPorCarrera($carrera_id){
        $sql = "SELECT i.*, u.nombre_completo as autor, c.nombre as carrera
                FROM ideas i
                JOIN usuarios u ON i.usuario_creador_id = u.id_usuario
                LEFT JOIN carreras c ON u.carrera_id = c.id_carrera
                WHERE u.carrera_id = :cid  -- AQUÍ ESTÁ EL FILTRO
                ORDER BY i.fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cid', $carrera_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 3. Obtener solicitudes pendientes (Solo para el dueño)
    public function obtenerSolicitudes($id_idea){
        $sql = "SELECT u.id_usuario, u.nombre_completo, c.nombre as carrera, im.mensaje_solicitud, im.fecha_union
                FROM idea_miembros im
                JOIN usuarios u ON im.usuario_id = u.id_usuario
                LEFT JOIN carreras c ON u.carrera_id = c.id_carrera
                WHERE im.idea_id = :id AND im.estado_solicitud = 'pendiente'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_idea);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 4. Aceptar o Rechazar solicitud
    public function gestionarSolicitud($id_idea, $id_usuario, $accion){
        // accion puede ser 'aceptado' o 'rechazado'
        $sql = "UPDATE idea_miembros SET estado_solicitud = :estado WHERE idea_id = :idea AND usuario_id = :user";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':estado', $accion);
        $stmt->bindValue(':idea', $id_idea);
        $stmt->bindValue(':user', $id_usuario);
        return $stmt->execute();
    }

    // 5. Cambiar estado de la Idea (Abierta -> En Desarrollo -> Cerrada)
    public function actualizarEstadoIdea($id_idea, $nuevo_estado){
        $sql = "UPDATE ideas SET estado = :estado WHERE id_idea = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':estado', $nuevo_estado);
        $stmt->bindValue(':id', $id_idea);
        return $stmt->execute();
    }

// --- MÉTODOS PARA VOTOS Y COMENTARIOS ---

    // 1. Contar votos totales de una idea
    public function contarVotos($id_idea){
        $sql = "SELECT COUNT(*) as total FROM idea_votos WHERE idea_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_idea);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->total;
    }

    // 2. Verificar si YO ya voté (para pintar el botón)
    public function usuarioVoto($id_idea, $id_usuario){
        $sql = "SELECT * FROM idea_votos WHERE idea_id = :id AND usuario_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_idea);
        $stmt->bindValue(':uid', $id_usuario);
        $stmt->execute();
        return $stmt->fetch(); // Retorna true si encontró algo
    }

    // 3. Dar o Quitar like (Toggle)
    public function alternarVoto($id_idea, $id_usuario){
        if($this->usuarioVoto($id_idea, $id_usuario)){
            // Si ya existe, lo borramos (Quitar like)
            $sql = "DELETE FROM idea_votos WHERE idea_id = :id AND usuario_id = :uid";
        } else {
            // Si no existe, lo creamos (Dar like)
            $sql = "INSERT INTO idea_votos (idea_id, usuario_id) VALUES (:id, :uid)";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_idea);
        $stmt->bindValue(':uid', $id_usuario);
        return $stmt->execute();
    }

    // 4. Obtener comentarios de una idea
    public function obtenerComentarios($id_idea){
        $sql = "SELECT c.*, u.nombre_completo, u.rol
                FROM idea_comentarios c
                JOIN usuarios u ON c.usuario_id = u.id_usuario
                WHERE c.idea_id = :id
                ORDER BY c.fecha_comentario ASC"; // Los más viejos arriba (tipo chat)
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_idea);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 5. Agregar un comentario
    public function agregarComentario($id_idea, $id_usuario, $comentario){
        $sql = "INSERT INTO idea_comentarios (idea_id, usuario_id, comentario) VALUES (:id, :uid, :com)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_idea);
        $stmt->bindValue(':uid', $id_usuario);
        $stmt->bindValue(':com', $comentario);
        return $stmt->execute();
    }

    // Actualizar una idea existente
    public function actualizar($datos){
        $sql = "UPDATE ideas SET titulo = :tit, descripcion = :desc WHERE id_idea = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tit', $datos['titulo']);
        $stmt->bindValue(':desc', $datos['descripcion']);
        $stmt->bindValue(':id', $datos['id']);
        return $stmt->execute();
    }

    // Eliminar una idea (físicamente)
    public function eliminar($id){
        $sql = "DELETE FROM ideas WHERE id_idea = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}