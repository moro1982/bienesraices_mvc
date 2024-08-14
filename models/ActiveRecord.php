<?php

namespace Model;

class ActiveRecord {

    //-> BBDD
    protected static $db;
    protected static $columnasBD = [];
    protected static $tabla = '';

    //-> Arreglo con mensajes de error
    protected static $errores = [];

    public static function setDB($database) {
        self::$db = $database;
    }

    public function guardar() {
        if (!is_null($this->id)) {
            //-> Actualizar registro
            $this->actualizar();
        } else {
            //-> Crear registro
            $this->crear();
        }
    }

    public function crear() {
        //-> Sanitizar entrada de datos
        $atributos = $this->sanitizarAtributos();
        //-> Insertar en la BBDD
        $query = "INSERT INTO " . static::$tabla . " (";
        $query .= join(", ", array_keys($atributos));
        $query .= ") VALUES ('"; 
        $query .= join("', '", array_values($atributos));
        $query .= "')";
        //-> Realiza consulta (INSERT)
        $resultado = self::$db->query($query);
        //-> Mensajes de éxito o error
        if ($resultado) {
            //-> Redireccionar al usuario
            header('location: /admin?resultado=1');
        }
    }

    public function actualizar() {
        //-> Sanitizar entrada de datos
        $atributos = $this->sanitizarAtributos();
        //-> Concatenar "llave = valor" y almacenarlos en un array
        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key} = '{$value}'";
        }
        //-> Construir el string del query
        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(", ", $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1";
        //-> Realizar consulta (UPDATE)
        $resultado = self::$db->query($query);
        if ($resultado) {
            // -> Redireccionar al usuario
            header('location: /admin?resultado=2');
        }
    }

    public function eliminar() {
        //-> Construir el string del query
        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        //-> Realizar consulta (DELETE)
        $resultado = self::$db->query($query);
        if ($resultado) {
            //-> Borrar imagen del servidor
            $this->borrarImagen();
            // -> Redireccionar
            header('location: /admin?resultado=3');
        }
    }

    //-> Identificar y mapear los atributos de la BBDD
    public function atributos() {
        $atributos = [];
        foreach (static::$columnasBD as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    //-> Sanitizar los atributos
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    //-> Subir archivos
    public function setImagen($imagen) {
        //-> Comprobar si existe el registro verificando si hay ID previo
        if (!is_null($this->id)) {
            //-> Comprobar si existe el archivo y eliminar la imagen previa
            $this->borrarImagen();
        }
        //-> Asignar nombre de la nueva imagen al atributo
        if ($imagen) {
            $this->imagen = $imagen;
        }
    }

    //-> Borrar archivos
    public function borrarImagen() {
        //-> Comprobar si existe el archivo y eliminar la imagen
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        if ($existeArchivo) {
            unlink(CARPETA_IMAGENES . $this->imagen);
        }
    }

    //-> Validaciones
    public static function getErrores() {
        return static::$errores;
    }

    public function validar() {
        static::$errores = [];
        return static::$errores;
    }

    //-> Traer todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    //-> Obtener determinado número de registros
    public static function get($cantidad) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    //-> Buscar un registro por su ID
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = {$id};";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    //-> Realizar la consulta y retornar un array de objetos (propiedades)
    public static function consultarSQL($query) {
        //-> Consultar la BBDD
        $resultado = self::$db->query($query);
        //-> Iterar resultados
        $array = [];
        while ( $registro = $resultado->fetch_assoc() ) {
            $array[] = static::crearObjeto($registro);
        }
        //-> Liberar memoria
        $resultado->free();
        //-> Retornar resultados
        return $array;
    }

    //-> Crear un objeto a partir de un arreglo asociativo
    protected static function crearObjeto($registro) {
        //-> Instanciamos (creamos objeto vacío)
        $objeto = new static;
        //-> Iteramos sobre el arreglo asociativo $registro discriminando llave y valor, mapeandolos en el objeto creado
        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key) ) {
                $objeto->$key = $value;
            }
        }
        //-> Retornamos el objeto creado
        return $objeto;
    }

    //-> Sincronizar el objeto en memoria con los cambios realizados por el usuario en formulario
    public function sincronizar( $args = [] ) {
        foreach ($args as $key => $value) {
            if ( property_exists($this, $key) && !is_null($value) ) {
                $this->$key = $value;
            }
        }
    }
}