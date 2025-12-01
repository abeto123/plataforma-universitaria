<?php
class Controller {
    
    // Cargar Modelo
    protected function model($model){
        // Usa APPROOT para encontrar el archivo sin errores
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    // Cargar Vista
    protected function view($view, $data = []){
        // Verifica si el archivo existe antes de cargarlo
        if(file_exists(APPROOT . '/views/' . $view . '.php')){
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die("La vista no existe: " . APPROOT . '/views/' . $view . '.php');
        }
    }

    protected function redirect($url){
        header('location: ' . BASE_URL . $url);
        exit();
    }

    protected function requireAuth(){
        if(!isset($_SESSION['usuario_id'])){
            $this->redirect('auth/login');
        }
    }

    protected function isLoggedIn(){
        return isset($_SESSION['usuario_id']);
    }
}
?>