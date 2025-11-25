<?php

class Controller{
    protected function model($model){
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    protected function view($view, $data = []){
        require_once '../app/views/' . $view . '.php';
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