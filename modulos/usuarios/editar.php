<?php 

require '../../config/base.php';
require $BASE_DIR . '/funciones.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
	// Si esta definido nuestro id en la url y no esta vacio
	if (isset($_GET['id']) and !empty($_GET['id']))
	{
		// Se obtiene los datos del usuario dado el id o una página 404 si es que no se encontraron resultados
		$usuario = get_object_or_404($conexion, 'usuarios', $_GET['id']);
	}
	else
	{
		// En caso de no estar definido el id en la url o si esta vacio su valor mostramos al usuario una página de 404 en el navegador
		header('Location: ' . $APP_URL . '404.php');
	}
}

$errores = '';

// Comprobar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Se obtienen los campos que necesito de mi formulario
	$id = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);
	$username = trim($_POST['username']);
	$username_first = $_POST['username_first'];
	$first_name = (!empty(trim($_POST['first_name']))) ? filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING) : NULL;
	$last_name = (!empty(trim($_POST['last_name']))) ? filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING) : NULL;
	$superuser = (isset($_POST['superuser'])) ? 1 : 0;
	$active = (isset($_POST['active'])) ? 1 : 0;

	// Validar nuestros campos del formulario
	// username como es el único campo obligatorio, comprobamos no este vacio
	if (empty($username))
	{
		// En caso de estar vacio la variable $username, enviamos un mensaje de error
		$errores .= 'Por favor ingresa un username <br>';
		
	}
	else
	{
		// Aplicamos un filtro a nuestra variable $username para limpiar etiquetas
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

		if ($resultado !== false && $username != $username_first)
		{
			$errores .= 'El username ya existe, por favor ingresa un username diferente <br>';
		}

		// Si no hubo errores se edita el registro
		if ($errores == '')
		{
			$fecha_hora_actual = date('Y-m-d H:i:s');
			$statement = $conexion->prepare('
				UPDATE usuarios
				SET username = :username,
					first_name = :first_name, 
					last_name = :last_name, 
					superuser = :superuser, 
					active = :active, 
					updated_at = :updated_at
				WHERE id = :id
			');
			$statement->execute(array(
				':username' => $username,
				':first_name' => $first_name,
				':last_name' => $last_name,
				':superuser' => $superuser,
				':active' => $active,
				':updated_at' => $fecha_hora_actual,
				':id' => $id
			));

			// Redirigir al index de usuarios
			header('Location: ' . $APP_URL . 'modulos/usuarios/index.php');
		}

		
	}

}

require $VIEWS_DIR . 'usuarios/editar.view.php';

?>