<?php
class NoticiaController extends Controller {

    public function index(){
        $noticiaModel = $this->model('Noticia');
        
        // Filtro por tipo (semillero, voluntariado, etc.)
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;

        $noticias = $noticiaModel->obtenerTodas($tipo);

        $datos = [
            'noticias' => $noticias,
            'filtro_tipo' => $tipo
        ];

        $this->view('noticias/index', $datos);
    }

    public function detalle($id){
        $noticiaModel = $this->model('Noticia');
        $noticia = $noticiaModel->obtenerPorId($id);

        if(!$noticia){ $this->redirect('noticia'); }

        $this->view('noticias/detalle', ['noticia' => $noticia]);
    }

    // Solo para ADMINISTRADORES
    public function crear(){
        $this->requireAuth();

        // Verificamos el ROL (Asumiendo que guardaste 'rol' en la sesión al hacer login)
        // Si no es admin, lo mandamos de vuelta al inicio
        if($_SESSION['usuario_rol'] != 'administrador'){
            $this->redirect('noticia');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = [
                'titulo' => trim($_POST['titulo']),
                'contenido' => trim($_POST['contenido']),
                'tipo' => $_POST['tipo'],
                'usuario_id' => $_SESSION['usuario_id']
            ];

            $noticiaModel = $this->model('Noticia');
            if($noticiaModel->crear($datos)){
                $this->redirect('noticia');
            }
        }

        $this->view('noticias/crear');
    }
}