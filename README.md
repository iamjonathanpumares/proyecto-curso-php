# Editar usuarios

Vamos primero a crear dos nuevos archivos, el primero lo vamos a crear dentro de nuestra carpeta modulos/usuarios y le vamos a poner editar.php y despues vamos a crear el otro archivo dentro de la carpeta views/usuarios y le ponemos editar.view.php

Recuerden que el primer archivo (editar.php) lo utilizamos para poner toda la lógica de negocios de nuestro módulo de editar usuarios y el segundo archivo (editar.view.php) lo utilizamos para crear la vista (parte visible para el usuario) en donde creamos la estructura de nuestro módulo, es decir, como se verá en el navegador.

Lo primero que haremos es requerir nuestra archivo de configuración que se encuentra dentro de la carpeta config, y luego requerir la vista así que abrimos nuestro archivo editar.php y escribimos lo siguiente:

```
require '../../config/base.php';

require $VIEWS_DIR . 'usuarios/editar.view.php';
```

Recuerden que la variable $VIEWS_DIR nos permite ubicar nuestra carpeta views.

Luego abrimos nuestro archivo editar.view.php y va a tener un contenido parecido a lo que teniamos en el archivo views/usuarios/crear.view.php, así que copiamos todo su contenido y lo pegamos en nuestro archivo editar.view.php

Editamos la parte del título para que ahora aparezca "Editar usuario"

Hasta el momento si abrimos en el navegador nuestro archivo editar.php que esta dentro de nuestra carpeta modulos/usuarios veremos nuestro formulario

Ahora vamos a agregar a nuestro formulario un campo de tipo hidden con un name que diga "id":

```
<input type="hidden" value="" name="id">
```

Ahora vamos a crear una función que nos permite obtener un registro de nuestra tabla usuarios dado el id, para ello vamos a crear un archivo en la raíz de nuestra carpeta del proyecto y le vamos a poner funciones.php que va a tener el siguiente contenido:

```
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
```

Les dejo comentado la función para que entienden cual es su funcionamiento.

Una vez hayamos terminado esta parte de nuestra función vamos a requerir el archivo funciones.php en nuestro archivo editar.php ya que vamos a utilizar la función que acabamos de crear en dicho archivo. Despues de haber requerido nuestro archivo de configuración vamos a agregar lo siguiente:

```
require $BASE_DIR . '/funciones.php';
```

Vamos ahora a obtener los datos del usuario a editar por medio del id para rellenar el formulario con ayuda de nuestra función que se creo en el archivo funciones.php

```
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
```

A nuestra función get_object_or_404 le pasamos los siguientes parametros: La conexión de nuestra base de datos que se encuentra dentro de nuestro archivo de configuración config/base.php, el segundo parametro es una cadena con el nombre de nuestra tabla que vamos a consultar, en este caso la tabla usuarios, y por último el id que se lo estamos pasando por el método GET a través de la url.

Si la consulta devuelve un resultado con los parametros dados nos devolverá un array con los datos del registro encontrado, así que ya podemos hacer uso de nuestra variable $usuario en nuestra vista editar.view.php, nuestro formulario quedaría de esta forma:

```
<!-- form start -->
<form role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
  <input type="hidden" value="<?php echo $usuario['id']; ?>" name="id">
  <div class="box-body">
    <div class="form-group">
      <label for="exampleInputEmail1">Usuario</label>
      <input type="text" name="username" class="form-control" id="id_username" placeholder="Ingrese un usuario" value="<?php echo $usuario['username']; ?>">
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">First name</label>
      <input type="text" name="first_name" class="form-control" id="id_first_name" placeholder="Ingrese un nombre" value="<?php echo $usuario['first_name']; ?>">
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">Last name</label>
      <input type="text" name="last_name" class="form-control" id="id_last_name" placeholder="Ingrese un apellido" value="<?php echo $usuario['last_name']; ?>">
    </div>
    <!-- checkbox -->
    <div class="form-group">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="superuser" id="id_superuser" value="1" <?php if ($usuario['superuser'] == 1) echo 'checked'; ?>>
          Superusuario
        </label>
      </div>

      <div class="checkbox">
        <label>
          <input type="checkbox" <?php if ($usuario['active'] == 1) echo 'checked'; ?> name="active" id="id_active" value="1">
          Activo
        </label>
      </div>
    </div>
  </div>
  <!-- /.box-body -->

  <div class="box-footer">
    <button type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
```

Si accedemos a nuestro módulo de editar usuarios por medio de la url y le pasamos un parametro id con algún valor conocido dentro de su tabla usuarios (http://proyecto.test/modulos/usuarios/editar.php?id=9) verán que se rellenan los datos en el formulario con ese usuario, en caso de que ese id no exista dentro de la tabla usuarios, devolverá una página 404 en el navegador.

Dense cuenta que en el input de tipo hidden que se agrego le pusimos en el atributo value el id del usuario que se quiere editar.

Ahora vamos a realizar dos acciones para complementar nuestro módulo de editar usuarios:

1. Agregar un botón de editar por cada fila de nuestra tabla en nuestro módulo index de usuarios, es decir, en el archivo de nuestra vista views/usuarios/index.view.php

Para ello agregamos una fila más a nuestra tabla de usuarios:

```
<td><a href="<?php echo $APP_URL . 'modulos/usuarios/editar.php?id=' . $usuario['id']; ?>" class="btn btn-success">Editar</a></td>
```

Quedando nuestra tabla de la siguiente forma:

```
<table id="example1" class="table table-bordered table-striped">
  <thead>
  <tr>
    <th>Username</th>
    <th>First name</th>
    <th>Last name</th>
    <th>Email</th>
    <th>Acciones</th>
  </tr>
  </thead>
  <tbody>
    
      <?php foreach ($usuarios as $usuario): ?>
    <tr> <!-- Table Row (Fila de la tabla) -->
      <td><?php echo $usuario['username']; ?></td>
      <td><?php echo $usuario['first_name']; ?>
      </td>
      <td><?php echo $usuario['last_name']; ?></td>
      <td> <?php echo $usuario['email']; ?></td>
      <td><a href="<?php echo $APP_URL . 'modulos/usuarios/editar.php?id=' . $usuario['id']; ?>" class="btn btn-success">Editar</a></td>
    </tr>
      <?php endforeach; ?>

    
    
  </tbody>
  <tfoot>
  <tr>
    <th>Username</th>
    <th>First name</th>
    <th>Last name</th>
    <th>Email</th>
    <th>Acciones</th>
  </tr>
  </tfoot>
</table>
```

2. Crear una página 404 por si no esta definido nuestro id en la url o esta vacio, otro caso en donde nos devolvería el 404 sería en caso de que la consulta no devuelva ningún resultado.
 
Entonces vamos a crear dos archivos, uno en la raíz de la carpeta de nuestro proyecto que le vamos a poner 404.php y otro dentro de la carpeta views y le ponemos 404.view.php (Recuerden que el primero es donde va la lógica de negocios y el otro es la vista)

Una vez creado nuestros dos archivos, vamos a ir al archivo 404.php y vamos a requerir dos archivos, nuestro archivo de configuración y la vista, el contenido es el siguiente:

```
<?php 

require 'config/base.php';

require $VIEWS_DIR . '404.view.php';

?>
```

En nuestro archivo de la vista, es decir, el archivo views/404.view.php, tendrá el siguiente contenido:

```
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'bower_components/bootstrap/dist/css/bootstrap.min.css'; ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'bower_components/font-awesome/css/font-awesome.min.css'; ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'bower_components/Ionicons/css/ionicons.min.css'; ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'dist/css/AdminLTE.min.css'; ?>">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'dist/css/skins/_all-skins.min.css'; ?>">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'bower_components/morris.js/morris.css'; ?>">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'bower_components/jvectormap/jquery-jvectormap.css'; ?>">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'bower_components/bootstrap-daterangepicker/daterangepicker.css'; ?>">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo $ASSETS_URL . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'; ?>">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
  <!-- Main content -->
  <section class="content">
    <div class="error-page">
      <h2 class="headline text-yellow"> 404</h2>

      <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> Oops! Página no encontrada</h3>

        <p>
          No pudimos encontrar la página que estabas buscando.
          Mientras tanto, puede <a href="<?php echo $APP_URL . 'index.php' ?>">regresar al dashboard</a>.
        </p>

      </div>
      <!-- /.error-content -->
    </div>
    <!-- /.error-page -->
  </section>

<!-- jQuery 3 -->
<script src="<?php echo $ASSETS_URL . 'bower_components/jquery/dist/jquery.min.js'; ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo $ASSETS_URL . 'bower_components/jquery-ui/jquery-ui.min.js'; ?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo $ASSETS_URL . 'bower_components/bootstrap/dist/js/bootstrap.min.js'; ?>"></script>
<!-- Morris.js charts -->
<script src="<?php echo $ASSETS_URL . 'bower_components/raphael/raphael.min.js'; ?>"></script>
<script src="<?php echo $ASSETS_URL . 'bower_components/morris.js/morris.min.js'; ?>"></script>
<!-- Sparkline -->
<script src="<?php echo $ASSETS_URL . 'bower_components/jquery-sparkline/dist/jquery.sparkline.min.js'; ?>"></script>
<!-- jvectormap -->
<script src="<?php echo $ASSETS_URL . 'plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'; ?>"></script>
<script src="<?php echo $ASSETS_URL . 'plugins/jvectormap/jquery-jvectormap-world-mill-en.js'; ?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo $ASSETS_URL . 'bower_components/jquery-knob/dist/jquery.knob.min.js'; ?>"></script>
<!-- daterangepicker -->
<script src="<?php echo $ASSETS_URL . 'bower_components/moment/min/moment.min.js'; ?>"></script>
<script src="<?php echo $ASSETS_URL . 'bower_components/bootstrap-daterangepicker/daterangepicker.js'; ?>"></script>
<!-- datepicker -->
<script src="<?php echo $ASSETS_URL . 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo $ASSETS_URL . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'; ?>"></script>
<!-- Slimscroll -->
<script src="<?php echo $ASSETS_URL . 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js'; ?>"></script>
<!-- FastClick -->
<script src="<?php echo $ASSETS_URL . 'bower_components/fastclick/lib/fastclick.js'; ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo $ASSETS_URL . 'dist/js/adminlte.min.js'; ?>"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo $ASSETS_URL . 'dist/js/pages/dashboard.js'; ?>"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo $ASSETS_URL . 'dist/js/demo.js'; ?>"></script>
</body>
</html>
```

Si nos damos cuenta ya tiene las referencias hacía nuestros archivos estaticos con ayuda de nuestra variable $ASSETS_URL.

Ahora si intentamos acceder a nuestra url (http://proyecto.test/modulos/usuarios/editar.php?id=67) con un id que no exista dentro de nuestra tabla usuarios o no le pasamos el parametro "id" (http://proyecto.test/modulos/usuarios/editar.php) nos devolverá una página 404 diciendo que no se pudo encontrar la página que estabas buscando.

Ya por último nuestro módulo de editar usuario vamos a comprobar si nuestro formulario ha sido enviado a través del método POST y hacer las comprobaciones correspondientes, y si no hubo errores editar los datos del usuario que nos pasaron a través del formulario. Por lo cual escribimos lo siguiente:

```
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
```

Les dejo comentado cada línea para que vean que acciones se realizan.

# Eliminar usuarios

Ahora vamos por el módulo de eliminar usuarios, para ello vamos a crear dos archivos, uno dentro de la carpeta modulos/usuarios y le vamos a poner eliminar.php y la vista en la carpeta views/usuarios y le ponemos eliminar.view.php, vamos a requerir nuestro archivo de configuración y la vista en nuestro archivo eliminar.php, tendrá el siguiente contenido:

```
require '../../config/base.php';

require $VIEWS_DIR . 'usuarios/eliminar.view.php';
```

Luego en nuestro archivo eliminar.view.php, vamos a copiar todo el contenido de nuestro archivo views/index.view.php y en la sección en donde se encuentra el Main content, tendrá el siguiente contenido:

```
<!-- Main content -->
<section class="content">
    <h1>¿Desea eliminar?</h1>
    <p>¿Desea eliminar al usuario?</p>

    <!-- form start -->
    <form role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="hidden" value="<?php echo $usuario['id']; ?>" name="id">
        
        <button type="submit" class="btn btn-danger">Si, deseo eliminarlo</button>
        <a href="<?php echo $APP_URL . 'modulos/usuarios/index.php'; ?>" class="btn btn-success">No, regresar al index de usuarios</a>
    </form>
</section>
```

Vamos ahora a nuestro archivo eliminar.php y ponemos algo parecido a lo que hicimos en nuestro archivo editar.php, en el que validamos si se esta pasando el parametro "id" a través de nuestra url y que no este vacio, el contenido es el siguiente:

```
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
```

Ahora tenemos que comprobar si el formulario ha sido enviado y realizar la acción de eliminar al usuario con el "id" que nos pasaron a través del formulario, tendrá el contenido siguiente:

```
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
```

Ya por último vamos a agregar un botón de eliminar por cada fila de nuestra tabla del módulo de index de usuarios, es decir, en el archivo views/usuarios/index.view.php. En la columna en donde se encuentra nuestro botón de "Editar" vamos a agregar el de "Eliminar", por lo que tendrá el contenido siguiente:

```
<td>
    <a href="<?php echo $APP_URL . 'modulos/usuarios/editar.php?id=' . $usuario['id']; ?>" class="btn btn-success">Editar</a>
    <a href="<?php echo $APP_URL . 'modulos/usuarios/eliminar.php?id=' . $usuario['id']; ?>" class="btn btn-danger">Eliminar</a>
</td>
```