<?php
class PerfilController extends Controller {

    public function __construct(){
        // Todo este controlador es privado
        $this->requireAuth();
    }

    public function index(){
        $usuarioModel = $this->model('Usuario');
        // 1. Cargamos el modelo de notificaciones
        $notiModel = $this->model('Notificacion'); 

        $id_usuario = $_SESSION['usuario_id'];

        $datosUsuario = $usuarioModel->obtenerPerfil($id_usuario);
        $stats = $usuarioModel->obtenerEstadisticas($id_usuario);
        $carreraModel = $this->model('Carrera');
        $carreras = $carreraModel->obtenerTodas();

        // 2. Obtenemos las notificaciones
        $misNotificaciones = $notiModel->obtenerMisNotificaciones($id_usuario);

        $data = [
            'usuario' => $datosUsuario,
            'stats' => $stats,
            'carreras' => $carreras,
            'notificaciones' => $misNotificaciones // 3. Las pasamos a la vista
        ];

        $this->view('perfil/index', $data);
    }

    // Método para cuando haces clic en una notificación (La marca como leída y te redirige)
    public function leer($id_notificacion){
        $notiModel = $this->model('Notificacion');
        
        // 1. Obtener la notificación para saber a dónde redirigir
        // (Aquí hago una consulta rápida directa por simplicidad, lo ideal es agregar un método obtenerPorId en el modelo)
        $db = Database::connect();
        $stmt = $db->prepare("SELECT enlace FROM notificaciones WHERE id_notificacion = :id");
        $stmt->execute([':id' => $id_notificacion]);
        $noti = $stmt->fetch(PDO::FETCH_OBJ);

        // 2. Marcar como leída
        $notiModel->marcarLeida($id_notificacion);

        // 3. Redirigir al enlace (ej: ir al detalle de la idea)
        if($noti){
            header('Location: ' . BASE_URL . $noti->enlace);
        } else {
            header('Location: ' . BASE_URL . 'perfil');
        }
    }

    public function actualizar(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $usuarioModel = $this->model('Usuario');
            
            $nombre = trim($_POST['nombre']);
            $carrera_id = $_POST['carrera_id'];
            $telefono = trim($_POST['telefono']); // Nuevo campo
            
            if($usuarioModel->actualizarDatos($_SESSION['usuario_id'], $nombre, $carrera_id, $telefono)){
                $_SESSION['usuario_nombre'] = $nombre;
                $this->redirect('perfil');
            }
        }
    }

    public function cambiar_password(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $pass_nueva = $_POST['password_nueva'];
            
            if(strlen($pass_nueva) < 4){
                // Validación básica
                $this->redirect('perfil'); 
                return;
            }

            $pass_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
            
            $usuarioModel = $this->model('Usuario');
            $usuarioModel->actualizarPassword($_SESSION['usuario_id'], $pass_hash);
            
            $this->redirect('perfil');
        }
    }
}