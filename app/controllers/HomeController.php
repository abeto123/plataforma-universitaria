<?php

require_once '../app/core/Controller.php';

class HomeController extends Controller {
    private $noticiaModel;
    private $ideaModel;
    private $proyectoModel;

    public function __construct() {
        $this->noticiaModel = $this->model('Noticia');
        $this->ideaModel = $this->model('Idea');
        $this->proyectoModel = $this->model('Proyecto');
    }

    public function index() {

        $noticias = $this->noticiaModel->getLatest(3);
        
        $ideas = $this->ideaModel->getLatest(3);
        
        $proyectos = $this->proyectoModel->getActive(3);

        $data = [
            'ideas' => $ideas,
            'noticias' => $noticias,
            'proyectos' => $proyectos
        ];

        $this->view('home/index', $data);
        
    }
}

?>