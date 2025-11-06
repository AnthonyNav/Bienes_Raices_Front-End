<?php 

    // Importar la conexion
    require 'includes/config/database.php';
    $db = conectaDB();
    $errores = [];
    // autentica el usuario
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        

        // Escapar los datos
        $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = mysqli_real_escape_string($db, $_POST['password']);

        // Validaciones
        if (!$email) {
            $errores[] = "El email es obligatorio o no es valido";
        }
        if (!$password) {
            $errores[] = "El password es obligatorio";
        }

        if (empty($errores)) {
            // Revisar si el usuario existe
            $query = "SELECT * FROM usuarios WHERE email = '$email'";
            $resultado = mysqli_query($db, $query);

            if ($resultado->num_rows) {
                // El usuario existe
                $usuario = mysqli_fetch_assoc($resultado);

                // Verificar el password
                $auth = password_verify($password, $usuario['password']);

                if ($auth) {
                    // El usuario esta autenticado
                    session_start();

                    // Llenar el arreglo de la sesion
                    $_SESSION['usuario'] = $usuario['email'];
                    $_SESSION['login'] = true;

                    header('Location: /admin/index.php');
                } else {
                    $errores[] = "El password es incorrecto";
                }
            } else {
                $errores[] = "El usuario no existe";
            }
        } 
    }   

    // Incluir las funciones y el header
    require 'includes/funciones.php';
    incluirTemplate('header');
 ?>
    <main class="contenedor seccion contenido-centrado">
        <h1>Iniciar sesion</h1>
        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" class="formulario" novalidate>
            <fieldset>
                <legend>Email y Password</legend>

                <label for="email">Email</label>
                <input type="email" placeholder="Tu Email" id="email" name="email" >

                <label for="password">Password</label>
                <input type="password" placeholder="Tu Password" id="password" name="password" >
            </fieldset>

            <input type="submit" value="Iniciar Sesion" class="boton boton-verde">
        </form>
    </main>

<?php 
    incluirTemplate('footer');
 ?>