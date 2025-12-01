<?php

class AuthController extends Controller {

    public function login(){
        // Si ya está logueado, mandar al inicio
        if($this->isLoggedIn()){
            $this->redirect('home/index');
        }

        $errores = '';

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 1. Recibir datos y limpiar
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // 2. Llamar al modelo
            $usuarioModel = $this->model('Usuario');
            $usuario = $usuarioModel->obtenerPorEmail($email);

            // 3. Verificar contraseña
            if($usuario && password_verify($password, $usuario->password_hash)){
                // 4. Crear Sesión
                $_SESSION['usuario_id'] = $usuario->id_usuario;
                $_SESSION['usuario_nombre'] = $usuario->nombre_completo;
                $_SESSION['usuario_rol'] = $usuario->rol;

                $this->redirect('home/index');
            } else {
                $errores = 'Credenciales incorrectas.';
            }
        }

        // Cargar vista
        $this->view('auth/login', ['error' => $errores]);
    }

    public function registro(){
        if($this->isLoggedIn()){
            $this->redirect('home/index');
        }
        
        $usuarioModel = $this->model('Usuario');
        $carreras = $usuarioModel->obtenerCarreras();
        $errores = '';

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'carrera' => $_POST['carrera']
            ];

            if($usuarioModel->registrar($datos)){
                // Redirigir al login tras registro exitoso
                $this->redirect('auth/login');
            } else {
                $errores = 'Hubo un error al registrarse.';
            }
        }

        $this->view('auth/registro', ['carreras' => $carreras, 'error' => $errores]);
    }

    public function logout(){
        session_unset();
        session_destroy();
        $this->redirect('auth/login');
    }
}