<?php

require_once '../app/core/Database.php';

class User {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    public function authenticate($email, $password) {
        $query = "SELECT * FROM usuarios WHERE correo_electronico = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['password_hash'] === $password) {
            return $user;
        }
        return false;
    }
    
    public function create($nombre, $email, $password, $carrera_id) {
        $query = "INSERT INTO usuarios (nombre_completo, correo_electronico, password_hash, carrera_id) 
                VALUES (:nombre, :email, :password, :carrera_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':carrera_id', $carrera_id);
        
        return $stmt->execute();
    }
    
    public function getCarreras() {
        $query = "SELECT * FROM carreras ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT id_usuario, nombre_completo, correo_electronico, carrera_id, rol, foto_perfil, fecha_registro 
                FROM usuarios WHERE id_usuario = :id LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener usuario por ID: " . $e->getMessage());
        }
    }
    
    public function update($id, $nombre, $email, $carrera_id) {
        $query = "UPDATE usuarios 
                SET nombre_completo = :nombre, 
                    correo_electronico = :email, 
                    carrera_id = :carrera_id,
                    
                WHERE id_usuario = :id";
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':carrera_id', $carrera_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>