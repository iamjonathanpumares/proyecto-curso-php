<?php 

require '../../config/base.php';

$statement = $conexion->prepare('
	SELECT *
	FROM usuarios
');
$statement->execute();
$usuarios = $statement->fetchAll();

require $VIEWS_DIR . 'usuarios/index.view.php';

?>