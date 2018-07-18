<?php 

require '../../config/base.php';
require $BASE_DIR . '/modulos/permisos.php';

$errores = '';

// Comprobar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Se obtienen los campos que necesito de mi formulario
	$username = $_POST['username'];
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];
	$first_name = (!empty(trim($_POST['first_name']))) ? filter_var($_POST['first_name'], FILTER_SANITIZE_STRING) : NULL;
	$last_name = (!empty(trim($_POST['last_name']))) ? filter_var($_POST['last_name'], FILTER_SANITIZE_STRING) : NULL;
	$superuser = (isset($_POST['superuser'])) ? 1 : 0;
	$active = (isset($_POST['active'])) ? 1 : 0;

	// Validar nuestros campos del formulario
	// username
	$username = trim($username);
	if (empty($username) or empty($password) or empty($confirm_password))
	{
		$errores .= 'Por favor rellena todos los campos <br>';
		
	}
	else
	{
		$username = filter_var($username, FILTER_SANITIZE_STRING);

		// Validar si nuestro username existe
		$statement = $conexion->prepare('
			SELECT *
			FROM usuarios
			WHERE username = :username
			LIMIT 1
		');
		$statement->execute(array(
			':username' => $username
		));
		$resultado = $statement->fetch();

		// var_dump($resultado);

		if ($resultado !== false)
		{
			$errores .= 'El username ya existe, por favor ingresa un username diferente <br>';
		}
		else
		{
			// Comprobar que las contraseñas sean iguales
			$password = hash('sha512', $password);
			$confirm_password = hash('sha512', $confirm_password);

			if ($password != $confirm_password)
			{
				$errores .= 'Las contraseñas no coinciden <br>';
			}

		}

		// Si no hubo errores se guarda el registro
		if ($errores == '')
		{
			$fecha_hora_actual = date('Y-m-d H:i:s');
			$statement = $conexion->prepare('
				INSERT INTO usuarios(username, password, first_name, last_name, superuser, active, created_at, updated_at) VALUES(:username, :password, :first_name, :last_name, :superuser, :active, :created_at, :updated_at)
			');
			$statement->execute(array(
				':username' => $username,
				':password' => $password,
				':first_name' => $first_name,
				':last_name' => $last_name,
				':superuser' => $superuser,
				':active' => $active,
				':created_at' => $fecha_hora_actual,
				':updated_at' => $fecha_hora_actual
			));

			// Redirigir al username al login
			header('Location: ' . $APP_URL . 'modulos/usuarios/index.php');
		}

		
	}

}

require $VIEWS_DIR . 'usuarios/crear.view.php';

?>