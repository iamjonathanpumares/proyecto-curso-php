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

// Comprobar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Se obtienen los campos que necesito de mi formulario
	$id = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);

	$statement = $conexion->prepare('
		DELETE FROM usuarios
		WHERE id = :id
	');
	$statement->execute(array(
		':id' => $id
	));

	// Redirigir al index de usuarios
	header('Location: ' . $APP_URL . 'modulos/usuarios/index.php');


}

require $VIEWS_DIR . 'usuarios/eliminar.view.php';

?>