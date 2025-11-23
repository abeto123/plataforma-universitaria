<?php

class Controller{
    protected function model($model){
        require_once '../models/' . $model . '.php';
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
        if(!isset($_SESSION['id_usuario'])){
            $this->redirect('auth/login');
        }
    }

    protected function isLoggedIn(){
        return isset($_SESSION['id_usuario']);
    }
}

?>