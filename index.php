<?php

require 'config/base.php';
// require 'funciones.php';

/*
 *
 *	Middlewares
 *
*/
// Login requerido
if (!isset($_SESSION['username']))
{
	header('Location: login.php');
} 

require $VIEWS_DIR . 'index.view.php';

?>