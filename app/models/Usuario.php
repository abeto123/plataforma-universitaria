<?php

class Usuario {
    private $db;

    public function __construct(){
        // Usamos el Singleton que definimos
        $this->db = Database::connect();
    }

    // Buscar usuario por email (Para el Login)
    public function obtenerPorEmail($email){
        $sql = "SELECT * FROM usuarios WHERE correo_electronico = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Registrar nuevo usuario
    public function registrar($datos){
        try {
            $sql = "INSERT INTO usuarios (nombre_completo, correo_electronico, password_hash, carrera_id, rol) 
                    VALUES (:nombre, :email, :pass, :carrera, 'estudiante')";
            
            $stmt = $this->db->prepare($sql);
            
            // Encriptar contraseña
            $passHash = password_hash($datos['password'], PASSWORD_DEFAULT);

            $stmt->bindValue(':nombre', $datos['nombre']);
            $stmt->bindValue(':email', $datos['email']);
            $stmt->bindValue(':pass', $passHash);
            $stmt->bindValue(':carrera', $datos['carrera']);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Método auxiliar para llenar el select de carreras en el registro
    public function obtenerCarreras(){
        $stmt = $this->db->query("SELECT * FROM carreras");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // --- NUEVOS MÉTODOS PARA PERFIL ---

    // 1. Obtener datos completos del perfil (incluyendo nombre de carrera)
    public function obtenerPerfil($id){
        $sql = "SELECT u.*, c.nombre as nombre_carrera 
                FROM usuarios u 
                LEFT JOIN carreras c ON u.carrera_id = c.id_carrera
                WHERE u.id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar datos básicos (Nombre, Carrera y Teléfono)
    public function actualizarDatos($id, $nombre, $carrera_id, $telefono){
        $sql = "UPDATE usuarios SET nombre_completo = :nom, carrera_id = :cid, telefono = :tel WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom', $nombre);
        $stmt->bindValue(':cid', $carrera_id);
        $stmt->bindValue(':tel', $telefono); // Nuevo campo
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    // 3. Cambiar contraseña
    public function actualizarPassword($id, $password_hash){
        $sql = "UPDATE usuarios SET password_hash = :pass WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pass', $password_hash);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    // 4. Obtener estadísticas personales (Gamificación)
    public function obtenerEstadisticas($id){
        // Contar ideas creadas
        $stmt1 = $this->db->prepare("SELECT COUNT(*) as total FROM ideas WHERE usuario_creador_id = :id");
        $stmt1->bindValue(':id', $id);
        $stmt1->execute();
        $ideas = $stmt1->fetch(PDO::FETCH_OBJ)->total;

        // Contar proyectos seguidos
        $stmt2 = $this->db->prepare("SELECT COUNT(*) as total FROM proyecto_seguidores WHERE usuario_id = :id");
        $stmt2->bindValue(':id', $id);
        $stmt2->execute();
        $proyectos = $stmt2->fetch(PDO::FETCH_OBJ)->total;

        // Contar participaciones en foros
        $stmt3 = $this->db->prepare("SELECT COUNT(*) as total FROM foro_publicaciones WHERE usuario_id = :id");
        $stmt3->bindValue(':id', $id);
        $stmt3->execute();
        $preguntas = $stmt3->fetch(PDO::FETCH_OBJ)->total;

        return (object)[
            'ideas' => $ideas,
            'proyectos_seguidos' => $proyectos,
            'preguntas' => $preguntas
        ];
    }
}