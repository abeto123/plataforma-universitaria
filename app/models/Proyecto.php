<?php

class Proyecto {

    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    // 1. Listar proyectos (con filtros opcionales)
    public function obtenerTodos($estado = null, $carrera_id = null){
        $sql = "SELECT p.*, u.nombre_completo as responsable 
                FROM proyectos p
                JOIN usuarios u ON p.usuario_creador_id = u.id_usuario
                WHERE 1=1"; // Truco para concatenar ANDs
        
        if($estado){
            $sql .= " AND p.estado = :estado";
        }
        
        // Si filtras por carrera, hacemos un JOIN extra a la tabla pivote
        if($carrera_id){
            $sql .= " AND p.id_proyecto IN (SELECT proyecto_id FROM proyecto_carreras WHERE carrera_id = :cid)";
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        if($estado) { $stmt->bindValue(':estado', $estado); }
        if($carrera_id) { $stmt->bindValue(':cid', $carrera_id); }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 2. Obtener un proyecto por ID
    public function obtenerPorId($id){
        $sql = "SELECT p.*, u.nombre_completo as responsable, u.foto_perfil
                FROM proyectos p
                JOIN usuarios u ON p.usuario_creador_id = u.id_usuario
                WHERE p.id_proyecto = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // 3. Obtener las carreras asociadas al proyecto
    public function obtenerCarrerasProyecto($id_proyecto){
        $sql = "SELECT c.nombre 
                FROM proyecto_carreras pc
                JOIN carreras c ON pc.carrera_id = c.id_carrera
                WHERE pc.proyecto_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id_proyecto);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 4. Crear Proyecto Manualmente
    public function crear($datos, $carreras_ids){
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO proyectos (nombre, descripcion, estado, usuario_creador_id, fecha_inicio) 
                    VALUES (:nom, :desc, 'vigente', :uid, CURDATE())";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':nom', $datos['nombre']);
            $stmt->bindValue(':desc', $datos['descripcion']);
            $stmt->bindValue(':uid', $datos['usuario_id']);
            $stmt->execute();
            
            $id_proyecto = $this->db->lastInsertId();

            // Insertar las carreras asociadas (Multicarrera)
            if(!empty($carreras_ids)){
                $sqlCarrera = "INSERT INTO proyecto_carreras (proyecto_id, carrera_id) VALUES (:pid, :cid)";
                $stmtC = $this->db->prepare($sqlCarrera);
                foreach($carreras_ids as $cid){
                    $stmtC->bindValue(':pid', $id_proyecto);
                    $stmtC->bindValue(':cid', $cid);
                    $stmtC->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch(Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 5. Sistema de Seguidores
    public function verificarSeguimiento($id_proyecto, $id_usuario){
        $sql = "SELECT * FROM proyecto_seguidores WHERE proyecto_id = :pid AND usuario_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pid', $id_proyecto);
        $stmt->bindValue(':uid', $id_usuario);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function alternarSeguimiento($id_proyecto, $id_usuario){
        if($this->verificarSeguimiento($id_proyecto, $id_usuario)){
            // Si ya sigue, borrar (Dejar de seguir)
            $sql = "DELETE FROM proyecto_seguidores WHERE proyecto_id = :pid AND usuario_id = :uid";
        } else {
            // Si no sigue, insertar (Seguir)
            $sql = "INSERT INTO proyecto_seguidores (proyecto_id, usuario_id) VALUES (:pid, :uid)";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pid', $id_proyecto);
        $stmt->bindValue(':uid', $id_usuario);
        return $stmt->execute();
    }
    
    // Contar seguidores
    public function contarSeguidores($id_proyecto){
        $sql = "SELECT COUNT(*) as total FROM proyecto_seguidores WHERE proyecto_id = :pid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pid', $id_proyecto);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->total;
    }

    public function obtenerRecientes($limite = 3){
        $sql = "SELECT p.*, u.nombre_completo as responsable 
                FROM proyectos p
                JOIN usuarios u ON p.usuario_creador_id = u.id_usuario
                WHERE p.estado = 'vigente'
                ORDER BY p.fecha_creacion DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Método para crear un proyecto automáticamente desde una idea
    public function crearDesdeIdea($idea){
        try {
            $this->db->beginTransaction();

            // 1. Insertar en la tabla Proyectos
            $sql = "INSERT INTO proyectos (nombre, descripcion, estado, usuario_creador_id, fecha_inicio, idea_origen_id) 
                    VALUES (:nombre, :desc, 'vigente', :uid, CURDATE(), :idea_id)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':nombre', $idea->titulo);
            $stmt->bindValue(':desc', $idea->descripcion);
            $stmt->bindValue(':uid', $idea->usuario_creador_id);
            $stmt->bindValue(':idea_id', $idea->id_idea);
            $stmt->execute();

            // Obtener el ID del nuevo proyecto
            $id_proyecto = $this->db->lastInsertId();

            // 2. Cerrar la Idea original para que nadie más interactúe
            $sqlUpdate = "UPDATE ideas SET estado = 'cerrada' WHERE id_idea = :id";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->bindValue(':id', $idea->id_idea);
            $stmtUpdate->execute();

            // 3. (Opcional) Mover miembros del equipo a seguidores del proyecto
            // Esto es un plus: copiamos los miembros de la idea a seguidores del proyecto
            $sqlCopy = "INSERT INTO proyecto_seguidores (proyecto_id, usuario_id)
                        SELECT :proy_id, usuario_id FROM idea_miembros 
                        WHERE idea_id = :idea_id AND estado_solicitud = 'aceptado'";
            $stmtCopy = $this->db->prepare($sqlCopy);
            $stmtCopy->bindValue(':proy_id', $id_proyecto);
            $stmtCopy->bindValue(':idea_id', $idea->id_idea);
            $stmtCopy->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}