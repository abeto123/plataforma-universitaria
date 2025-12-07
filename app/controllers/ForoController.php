<?php
class ForoController extends Controller {

    public function index(){
        $foroModel = $this->model('Foro');
        
        // Filtros
        $cat = isset($_GET['categoria']) ? $_GET['categoria'] : null;
        $busq = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : null;

        $preguntas = $foroModel->obtenerPreguntas($cat, $busq);
        $categorias = $foroModel->obtenerCategorias();

        $datos = [
            'preguntas' => $preguntas,
            'categorias' => $categorias,
            'filtro_cat' => $cat,
            'busqueda' => $busq
        ];

        $this->view('foro/index', $datos);
    }

    public function crear(){
        $this->requireAuth();
        $foroModel = $this->model('Foro');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = [
                'titulo' => trim($_POST['titulo']),
                'contenido' => trim($_POST['contenido']),
                'categoria_id' => $_POST['categoria_id'],
                'usuario_id' => $_SESSION['usuario_id']
            ];

            if($foroModel->crearPregunta($datos)){
                $this->redirect('foro');
            }
        }

        $categorias = $foroModel->obtenerCategorias();
        $this->view('foro/crear', ['categorias' => $categorias]);
    }

    public function editar($id){
        $this->requireAuth();
        $foroModel = $this->model('Foro');
        $pregunta = $foroModel->obtenerPreguntaPorId($id);

        if($pregunta->usuario_id != $_SESSION['usuario_id']){
            $this->redirect('foro');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $foroModel->actualizarPregunta($id, $_POST['titulo'], $_POST['contenido']);
            $this->redirect('foro/detalle/' . $id);
        }
        $this->view('foro/editar', ['pregunta' => $pregunta]);
    }

    public function eliminar($id){
        $this->requireAuth();
        $foroModel = $this->model('Foro');
        $pregunta = $foroModel->obtenerPreguntaPorId($id);

        if($pregunta->usuario_id == $_SESSION['usuario_id']){
            $foroModel->eliminarPregunta($id);
        }
        $this->redirect('foro');
    }

    public function detalle($id){
        $foroModel = $this->model('Foro');
        
        $pregunta = $foroModel->obtenerPreguntaPorId($id);
        if(!$pregunta) { $this->redirect('foro'); }

        $respuestas = $foroModel->obtenerRespuestas($id);

        $datos = [
            'pregunta' => $pregunta,
            'respuestas' => $respuestas
        ];

        $this->view('foro/detalle', $datos);
    }

    public function responder($id_pregunta){
        $this->requireAuth();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $foroModel = $this->model('Foro');
            $datos = [
                'contenido' => trim($_POST['contenido']),
                'publicacion_id' => $id_pregunta,
                'usuario_id' => $_SESSION['usuario_id']
            ];
            $foroModel->responder($datos);
        }
        $this->redirect('foro/detalle/' . $id_pregunta);
    }
}