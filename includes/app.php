<?php

use Model\ActiveRecord;
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require 'funciones.php';
require 'config/database.php';

//-> Conectar a la BBDD
$db = conectarDB();
$db->set_charset('utf8');
ActiveRecord::setDB($db);