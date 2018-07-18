<?php

/*
 *
 * Función que retorna un registro de la base de datos en caso de encontrar, caso contrario devuelve una página 404 en el navegador
 * @param PDO $conexion
 * @param string $nombre_tabla
 * @param string $id
 * @return array or 404.php
 */
function get_object_or_404($conexion, $nombre_tabla, $id)
{
   // Aplicamos un filtro para limpiar etiquetas y quitar espacios en blanco para el id
   $id = filter_var(trim($id), FILTER_SANITIZE_STRING);

   // Preparamos nuestra consulta que nos devolverá un registro si es que existe
   $statement = $conexion->prepare("
      SELECT *
      FROM $nombre_tabla
      WHERE id = :id
   ");

   // Ejecutamos nuestra consulta y pasamos los parametros
   $statement->execute(array(
      ':id' => $id
   ));

   // El método fetch() solamente nos devuelve un resultado o false si no encontro ninguno
   $resultado = $statement->fetch();

   // En caso de ser diferente de false, es decir, si encontro resultados en la consulta
   if ($resultado !== false)
   {
      // Retorna el arreglo con los datos del registro encontrado
      return $resultado;
   }
   else
   {
      // Devuelve una página 404 en el navegador
      header('Location: ' . $GLOBALS['APP_URL'] . '404.php');
   }
}

?>