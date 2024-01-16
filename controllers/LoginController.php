<?php

namespace Controller;
use MVC\Router;
use Model\Admin;

class LoginController {

    public static function login(Router $router) {
        $errores = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           $auth = new Admin($_POST);
           $errores = $auth->validar();
            if (empty($errores)) {
                //-> Verificar si usuarix existe
                $resultado = $auth->existeUsuario();
                if (!$resultado) {
                    //-> Si no hay resultado, traer mensajes de error
                    $errores = Admin::getErrores();
                } else {
                    //-> Verificar contraseña
                    $autenticado = $auth->comprobarPassword($resultado);
                    if ($autenticado) {
                        //-> Autenticar usuarix
                        $auth->autenticar();
                    } else {
                        //-> Password Incorrecto -> Traer Mensajes de error
                        $errores = Admin::getErrores();
                    }
                }
            }
        }
        $router->render('auth/login', [
            'errores' => $errores
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }
}