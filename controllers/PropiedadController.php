<?php

namespace Controller;
use MVC\Router;
use Model\Propiedad;
use Model\Vendedor;
use Intervention\Image\ImageManagerStatic as Image;

class PropiedadController {
    public static function index(Router $router) {
        // -> Trae datos de todas las propiedades
        $propiedades = Propiedad::all();
        // -> Trae datos de todxs lxs vendedorxs
        $vendedores = Vendedor::all();
        // -> Muestra mensaje condicional
        $resultado = $_GET['resultado'] ?? null;

        $router->render('propiedades/admin', [
            'propiedades' => $propiedades,
            'vendedores' => $vendedores,
            'resultado' => $resultado
        ]);
    }
    public static function crear(Router $router) {
        $propiedad = new Propiedad;
        $vendedores = Vendedor::all();
        $errores = Propiedad::getErrores();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            //-> Instanciamos la clase
            $propiedad = new Propiedad($_POST['propiedad']);

            /* PREPARACIÓN DE ARCHIVOS PARA SUBIR */
            //-> Generar un nombre de archivo de imagen único
            $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";
        
            //-> Verificar si hay imagen en el superglobal $_FILES
            if ($_FILES['propiedad']['tmp_name']['imagen']) {
                /* Setear la imagen */
                //-> Resize de la imagen con Intervention Image
                $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800, 600);
                //-> Asigno nombre de la imagen al atributo de imagen de la instancia
                $propiedad->setImagen($nombreImagen);
            }

            //-> Validamos los datos recibidos y almacenamos los errores obtenidos
            $errores = $propiedad->validar();

            //-> Revisar que el arreglo de errores esté vacío y subir la información.
            if (empty($errores) ) {
                //-> Crear carpeta de imágenes
                if ( !is_dir(CARPETA_IMAGENES) ) {
                    mkdir(CARPETA_IMAGENES);
                }
                //-> Guardar la imagen en el servidor
                $image->save(CARPETA_IMAGENES . $nombreImagen);
                //-> Guardar datos de los atributos
                $propiedad->guardar();
            }
        }

        $router->render('propiedades/crear', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }
    
    public static function actualizar(Router $router) {
        $id = validarORedireccionar('/admin');

        $propiedad = Propiedad::find($id);
        $vendedores = Vendedor::all();
        $errores = Propiedad::getErrores();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            //-> Asignar los atributos
            $args = $_POST['propiedad'];
            //-> Sincronizar datos del $_POST con datos del objeto en memoria
            $propiedad->sincronizar($args);
            //-> Validaciones
            $errores = $propiedad->validar();
            /* Subida de archivos */
            //-> Generar un nombre de archivo de imagen único
            $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";
            //-> Verificar si hay imagen en el superglobal $_FILES
            if ($_FILES['propiedad']['tmp_name']['imagen']) {
                /* Setear la imagen */
                //-> Resize de la imagen con Intervention Image
                $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800, 600);
                //-> Asigno nombre de la imagen al atributo de imagen de la instancia
                $propiedad->setImagen($nombreImagen);
            }
            //-> Revisar que el arreglo de errores esté vacío y subir la información.
            if (empty($errores) ) {
                if ($_FILES['propiedad']['tmp_name']['imagen']) {
                    //-> Almacenar la imagen en el servidor
                    $image->save(CARPETA_IMAGENES . $nombreImagen);
                }
                //-> Guardar datos
                $propiedad->guardar();
            }
        }

        $router->render('propiedades/actualizar', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }

    public static function eliminar() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            //-> Almacenamos el valor del atributo 'id' del $_POST en una variable y lo sanitizamos
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            //-> Verificamos si hay valor de $id
            if ($id) {
                $tipo = $_POST['tipo']; //-> Almacenamos el valor atributo 'tipo' del $_POST en una variable
                //-> Verificamos si el valor del atributo 'tipo' se encuentra entre los permitidos
                if (validarTipoContenido($tipo)) {
                    $propiedad = Propiedad::find($id);  //-> Encontrar la propiedad
                    $propiedad->eliminar(); //-> Eliminar registro
                }
            }
        }
    }
}