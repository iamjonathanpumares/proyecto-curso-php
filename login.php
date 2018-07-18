<?php

require 'config/base.php';

$errores = '';

// Comprobar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Se obtienen los campos del formulario
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Validar si nuestros campos fueron llenados
	if (empty($username) or empty($password))
	{
		$errores .= 'Por favor rellena todos los campos <br>';
	}
	else
	{
		$username = filter_var($username, FILTER_SANITIZE_STRING);
		$password = hash('sha512', $password);

		// Comprobar si el usuario y contraseña son correctos
		$statement = $conexion->prepare('
			SELECT *
			FROM usuarios
			WHERE username = :username
				AND password = :password
		');
		$statement->execute(array(
			':username' => $username,
			':password' => $password
		));
		$resultado = $statement->fetch();

		if ($resultado !== false)
		{
			// Los datos fueron correctos
			$_SESSION['username'] = $username;
			$_SESSION['mensajes'] = [];

			// Redirigir al index.php
			header('Location: index.php');
		}
		else
		{
			// Los datos no fueron correctos
			$errores .= 'Datos incorrectos';
		}
	}
}

require $VIEWS_DIR . 'login.view.php';

?>