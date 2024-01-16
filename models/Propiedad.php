<?php

namespace Model;

class Propiedad extends ActiveRecord {
    
    protected static $tabla = 'propiedades';
    protected static $columnasBD = ['id', 'titulo', 'precio', 'imagen', 'descripcion', 'habitaciones', 'wc', 'estacionamiento', 'creado', 'vendedores_id'];

    public $id;
    public $titulo;
    public $precio;
    public $imagen;
    public $descripcion;
    public $habitaciones;
    public $wc;
    public $estacionamiento;
    public $creado;
    public $vendedores_id;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->titulo = $args['titulo'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->habitaciones = $args['habitaciones'] ?? '';
        $this->wc = $args['wc'] ?? '';
        $this->estacionamiento = $args['estacionamiento'] ?? '';
        $this->creado = date('Y/m/d');
        $this->vendedores_id = $args['vendedores_id'] ?? '';
    }

    public function validar() {
        if (!$this->titulo) {
            self::$errores[] = "Debes añadir un Título";
        }
        if (!$this->precio) {
            self::$errores[] = "El Precio es Obligatorio";
        }
        if (strlen($this->descripcion) < 50) {
            self::$errores[] = "La Descripción es Obligatoria y debe contener al menos 50 caracteres";
        }
        if (!$this->habitaciones) {
            self::$errores[] = "El número de Habitaciones es Obligatorio";
        }
        if (!$this->wc) {
            self::$errores[] = "El número de Baños es Obligatorio";
        }
        if (!$this->estacionamiento) {
            self::$errores[] = "El número de lugares de Estacionamiento es Obligatorio";
        }
        if (!$this->vendedores_id) {
            self::$errores[] = "Elige un Vendedor";
        }
        if (!$this->imagen) {
            self::$errores[] = "La Imagen es Obligatoria";
        }

        return self::$errores;
    }
}