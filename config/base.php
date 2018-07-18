<?php

session_start();

require_once 'conexion.php';

$BASE_DIR = dirname(__DIR__);
// Ruta absoluta del directorio padre
// /Users/jepumares/Sites/proyecto/config
// C://xammp/htdocs

$VIEWS_DIR = $BASE_DIR . '/views/';

$APP_URL = 'http://proyecto.test/';
// http://localhost/WEB2/practicas/proyecto/

$ASSETS_URL = $APP_URL . 'public/';

$DB_CONFIG = array(
	'basedatos' => 'practica_proyecto',
	'usuario' => 'root',
	'pass' => ''
);

$MEDIA_ROOT = $BASE_DIR . '/media/';

$MEDIA_URL = $APP_URL . 'media/';

$FUNCIONES_FILE = $BASE_DIR . '/funciones.php';

$conexion = conectar($DB_CONFIG['basedatos'], $DB_CONFIG['usuario'], $DB_CONFIG['pass']);

?>