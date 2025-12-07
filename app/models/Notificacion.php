<?php
class Notificacion {
    private $db;
    public function __construct(){ $this->db = Database::connect(); }

    // Crear una notificación
    public function crear($usuario_id, $mensaje, $enlace){
        $sql = "INSERT INTO notificaciones (usuario_id, mensaje, enlace) VALUES (:uid, :msg, :link)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid'=>$usuario_id, ':msg'=>$mensaje, ':link'=>$enlace]);
    }

    // Contar NO leídas
    public function contarNoLeidas($usuario_id){
        $sql = "SELECT COUNT(*) as total FROM notificaciones WHERE usuario_id = :uid AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid'=>$usuario_id]);
        return $stmt->fetch(PDO::FETCH_OBJ)->total;
    }

    // Obtener todas las notificaciones de un usuario
    public function obtenerMisNotificaciones($usuario_id){
        $sql = "SELECT * FROM notificaciones WHERE usuario_id = :uid ORDER BY fecha_creacion DESC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid'=>$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Marcar una notificación como LEÍDA
    public function marcarLeida($id_notificacion){
        $sql = "UPDATE notificaciones SET leida = 1 WHERE id_notificacion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id'=>$id_notificacion]);
    }
}