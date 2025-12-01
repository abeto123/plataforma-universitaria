<?php
class PerfilController extends Controller {

    public function __construct(){
        // Todo este controlador es privado
        $this->requireAuth();
    }

    public function index(){
        $usuarioModel = $this->model('Usuario');
        $id_usuario = $_SESSION['usuario_id'];

        $datosUsuario = $usuarioModel->obtenerPerfil($id_usuario);
        $stats = $usuarioModel->obtenerEstadisticas($id_usuario);
        
        // Obtener lista de carreras para el formulario de edición
        $carreraModel = $this->model('Carrera'); // Asegúrate de tener este modelo (lo creamos antes)
        $carreras = $carreraModel->obtenerTodas();

        $data = [
            'usuario' => $datosUsuario,
            'stats' => $stats,
            'carreras' => $carreras
        ];

        $this->view('perfil/index', $data);
    }

    public function actualizar(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $usuarioModel = $this->model('Usuario');
            
            $nombre = trim($_POST['nombre']);
            $carrera_id = $_POST['carrera_id'];
            
            // Actualizar nombre en BD y en Sesión
            if($usuarioModel->actualizarDatos($_SESSION['usuario_id'], $nombre, $carrera_id)){
                $_SESSION['usuario_nombre'] = $nombre;
                // Redirigir con éxito (podrías agregar un mensaje flash si quisieras)
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