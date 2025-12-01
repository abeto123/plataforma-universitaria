<?php
class ProyectoController extends Controller {

    public function index(){
        $proyectoModel = $this->model('Proyecto');
        $carreraModel = $this->model('Carrera');

        // Filtros
        $filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : null;
        $filtroCarrera = isset($_GET['carrera']) ? $_GET['carrera'] : null;

        $proyectos = $proyectoModel->obtenerTodos($filtroEstado, $filtroCarrera);
        $carreras = $carreraModel->obtenerTodas();

        $datos = [
            'proyectos' => $proyectos,
            'carreras' => $carreras,
            'f_estado' => $filtroEstado,
            'f_carrera' => $filtroCarrera
        ];

        $this->view('proyectos/index', $datos);
    }

    public function crear(){
        $this->requireAuth();
        
        $carreraModel = $this->model('Carrera');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion']),
                'usuario_id' => $_SESSION['usuario_id']
            ];
            // Array de carreras seleccionadas (checkboxes)
            $carreras_seleccionadas = isset($_POST['carreras']) ? $_POST['carreras'] : [];

            $proyectoModel = $this->model('Proyecto');
            if($proyectoModel->crear($datos, $carreras_seleccionadas)){
                $this->redirect('proyecto');
            }
        }

        $carreras = $carreraModel->obtenerTodas();
        $this->view('proyectos/crear', ['carreras' => $carreras]);
    }

    public function detalle($id){
        $proyectoModel = $this->model('Proyecto');
        
        $proyecto = $proyectoModel->obtenerPorId($id);
        if(!$proyecto) { $this->redirect('proyecto'); }

        $carreras = $proyectoModel->obtenerCarrerasProyecto($id);
        $numSeguidores = $proyectoModel->contarSeguidores($id);
        
        $sigoAlProyecto = false;
        if(isset($_SESSION['usuario_id'])){
            $sigoAlProyecto = $proyectoModel->verificarSeguimiento($id, $_SESSION['usuario_id']);
        }

        $datos = [
            'proyecto' => $proyecto,
            'carreras' => $carreras,
            'seguidores' => $numSeguidores,
            'siguiendo' => $sigoAlProyecto
        ];

        $this->view('proyectos/detalle', $datos);
    }

    // Acción para el botón "Seguir"
    public function seguir($id){
        $this->requireAuth();
        $proyectoModel = $this->model('Proyecto');
        $proyectoModel->alternarSeguimiento($id, $_SESSION['usuario_id']);
        
        // Volver al detalle
        $this->redirect('proyecto/detalle/' . $id);
    }
}