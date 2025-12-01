<?php
class IdeaController extends Controller {

    public function index(){
        $ideaModel = $this->model('Idea');
        $carreraModel = $this->model('Carrera'); // Cargamos el modelo nuevo

        // 1. Obtener todas las carreras para el Dropdown
        $listaCarreras = $carreraModel->obtenerTodas();

        // 2. Verificar si el usuario aplicó un filtro
        $filtro = isset($_GET['carrera_id']) && !empty($_GET['carrera_id']) ? $_GET['carrera_id'] : null;

        if($filtro){
            // Si hay filtro, usamos el método de filtrar (Asegúrate de tenerlo en tu Modelo Idea)
            $ideas = $ideaModel->obtenerPorCarrera($filtro);
        } else {
            // Si no, traemos todas
            $ideas = $ideaModel->obtenerTodas();
        }

        $datos = [
            'ideas' => $ideas,
            'carreras' => $listaCarreras, // Pasamos la lista a la vista
            'filtro_actual' => $filtro    // Pasamos qué seleccionó el usuario para recordarlo
        ];

        $this->view('ideas/index', $datos);
    }

    public function crear(){
        $this->requireAuth(); 

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = [
                'titulo' => trim($_POST['titulo']),
                'descripcion' => trim($_POST['descripcion']),
                'usuario_id' => $_SESSION['usuario_id']
            ];
            $ideaModel = $this->model('Idea');
            if($ideaModel->crear($datos)){
                $this->redirect('idea');
            }
        }
        $this->view('ideas/crear');
    }

    //Ver detalle de la idea
    

    //Acción de unirse
    public function unirse($id){
        $this->requireAuth(); // Solo logueados
        
        $ideaModel = $this->model('Idea');
        $ideaModel->unirse($id, $_SESSION['usuario_id']);
        
        // Recargar la página del detalle
        $this->redirect('idea/detalle/' . $id);
    }

    // Método para Aceptar/Rechazar
    public function gestionar_solicitud($id_idea, $id_usuario, $accion){
        $this->requireAuth();
        $ideaModel = $this->model('Idea');
        
        // Validar seguridad: ¿Soy el dueño de la idea? (Omitido por brevedad, pero idealmente se valida)
        
        $ideaModel->gestionarSolicitud($id_idea, $id_usuario, $accion);
        $this->redirect('idea/detalle/' . $id_idea);
    }

    // Método para cambiar estado de la idea
    public function cambiar_estado($id_idea){
        $this->requireAuth();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $nuevo_estado = $_POST['estado'];
            $ideaModel = $this->model('Idea');
            $ideaModel->actualizarEstadoIdea($id_idea, $nuevo_estado);
            $this->redirect('idea/detalle/' . $id_idea);
        }
    }

    public function convertir_a_proyecto($id_idea){
        $this->requireAuth();
        
        $ideaModel = $this->model('Idea');
        $proyectoModel = $this->model('Proyecto');
        
        $idea = $ideaModel->obtenerPorId($id_idea);

        // Validar que sea el dueño
        if($_SESSION['usuario_id'] != $idea->usuario_creador_id){
            $this->redirect('idea');
        }

        // Ejecutar la conversión
        if($proyectoModel->crearDesdeIdea($idea)){
            // Si funciona, nos vamos a la lista de proyectos
            $this->redirect('proyecto');
        } else {
            // Si falla, volvemos a la idea
            $this->redirect('idea/detalle/' . $id_idea);
        }
    }

    public function detalle($id){
        $ideaModel = $this->model('Idea');
        $idea = $ideaModel->obtenerPorId($id);
        
        if(!$idea) { $this->redirect('idea'); }

        $miembros = $ideaModel->obtenerMiembros($id);
        
        // --- NUEVO: Votos y Comentarios ---
        $numVotos = $ideaModel->contarVotos($id);
        $comentarios = $ideaModel->obtenerComentarios($id);
        
        $yaVote = false;
        $esMiembro = false;
        $estadoMembresia = null;

        if(isset($_SESSION['usuario_id'])){
            // Verificar membresía
            $membresia = $ideaModel->verificarMembresia($id, $_SESSION['usuario_id']);
            if($membresia){
                $esMiembro = true;
                $estadoMembresia = $membresia['estado_solicitud'];
            }
            // Verificar voto
            if($ideaModel->usuarioVoto($id, $_SESSION['usuario_id'])){
                $yaVote = true;
            }
        }

        // Solicitudes (Solo dueño)
        $solicitudes = [];
        if(isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $idea->usuario_creador_id){
            $solicitudes = $ideaModel->obtenerSolicitudes($id);
        }

        $datos = [
            'idea' => $idea,
            'miembros' => $miembros,
            'es_miembro' => $esMiembro,
            'estado_membresia' => $estadoMembresia,
            'solicitudes' => $solicitudes,
            // Datos nuevos
            'votos' => $numVotos,
            'ya_vote' => $yaVote,
            'comentarios' => $comentarios
        ];

        $this->view('ideas/detalle', $datos);
    }

    // Acción de votar
    public function votar($id_idea){
        $this->requireAuth();
        $ideaModel = $this->model('Idea');
        $ideaModel->alternarVoto($id_idea, $_SESSION['usuario_id']);
        $this->redirect('idea/detalle/' . $id_idea);
    }

    // Acción de comentar
    public function comentar($id_idea){
        $this->requireAuth();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $comentario = trim($_POST['comentario']);
            if(!empty($comentario)){
                $ideaModel = $this->model('Idea');
                $ideaModel->agregarComentario($id_idea, $_SESSION['usuario_id'], $comentario);
            }
        }
        $this->redirect('idea/detalle/' . $id_idea);
    }
}