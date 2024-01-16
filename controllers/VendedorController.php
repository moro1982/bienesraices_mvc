<?php

namespace Controller;

use MVC\Router;
use Model\Vendedor;

class VendedorController {

    public static function crear(Router $router) {
        $vendedor = new Vendedor;
        $errores = Vendedor::getErrores();
        //-> Ejecutar el código después de que el usuario envía el formulario
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $vendedor = new Vendedor($_POST['vendedor']);
            $errores = $vendedor->validar();

            if (empty($errores)) {
                $vendedor->guardar();
            }
        }
        //-> Renderizar la vista pasandole los datos obtenidos
        $router->render('/vendedores/crear', [
            'vendedor' => $vendedor,
            'errores' => $errores
        ]);
    }

    public static function actualizar(Router $router) {
        //-> Validar si recibimos ID de vendedor, o redireccionar al admin
        $id = validarORedireccionar('/admin');
        //-> Obtener datos del vendedor a partir del ID
        $vendedor = Vendedor::find($id);
        //-> Obtener array de errores
        $errores = Vendedor::getErrores();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            //-> Asignar los valores del $_POST a una variable (arreglo asociativo)
            $args = $_POST['vendedor'];
            //-> Sincronizar datos del $_POST con datos del objeto en memoria
            $vendedor->sincronizar($args);
            //-> Validaciones
            $errores = $vendedor->validar();
    
            if (empty($errores)) {
                $vendedor->guardar();
            }
        }
        
        //-> Renderizar la vista junto con los datos obtenidos
        $router->render('/vendedores/actualizar', [
            'vendedor' => $vendedor,
            'errores' => $errores
        ]);
    }

    public static function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //-> Valida ID
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if ($id) {
                //-> Valida tipo (tabla) del cual eliminar
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo)) {
                    $vendedor = Vendedor::find($id);
                    $vendedor->eliminar();
                }
            }
        }
    }
}
