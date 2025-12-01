<?php
class HomeController extends Controller {
    
    public function index() {
        // 1. Instanciar modelos
        $ideaModel = $this->model('Idea');
        $noticiaModel = $this->model('Noticia');
        $proyectoModel = $this->model('Proyecto');

        // 2. Obtener datos (Los 3 más recientes de cada uno)
        $datos = [
            'ideas' => $ideaModel->obtenerRecientes(3),
            'noticias' => $noticiaModel->obtenerRecientes(3),
            'proyectos' => $proyectoModel->obtenerRecientes(3)
        ];

        // 3. Cargar vista
        $this->view('home/index', $datos);
    }
}