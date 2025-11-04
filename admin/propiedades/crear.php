<?php 
    // Conexion a la base de datos
    require '../../includes/config/database.php';
    $db = conectaDB();

    // Consulta para obtener los vendedores
    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

    // Arreglo con mensajes de errores
    $errores = [];

    $titulo = '';
    $precio = '';
    $descripcion = '';
    $habitaciones = '';
    $wc = '';
    $estacionamiento = '';
    $vendedorID = '';
    $imagen = '';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){


        $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
        $precio = mysqli_real_escape_string($db, $_POST['precio']);
        $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
        $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
        $wc = mysqli_real_escape_string($db, $_POST['wc']);
        $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
        $vendedorID = mysqli_real_escape_string($db, $_POST['vendedor']);

        // Asignar files hacia una variable
        $imagen = $_FILES['imagen'];

        // Validar que los campos no esten vacios
        if(!$titulo){
            $errores[] = "Debes añadir un titulo";
        }

        if(!$precio){
            $errores[] = "Debes añadir un precio";
        }

        if(!$descripcion){
            $errores[] = "Debes añadir una descripcion";
        }

        if (strlen($descripcion) < 50 ){
            $errores[] = "La descripcion debe tener al menos 50 caracteres";
        }

        if(!$habitaciones){
            $errores[] = "Debes añadir el numero de habitaciones";
        }

        if(!$wc){
            $errores[] = "Debes añadir el numero de baños";
        }

        if(!$estacionamiento){
            $errores[] = "Debes añadir el numero de lugares de estacionamiento";
        }

        if(!$vendedorID){
            $errores[] = "Debes seleccionar un vendedor";
        }

        if(!$imagen['name'] || $imagen['error']){
            $errores[] = "La imagen es obligatoria";
        }

        // Validar por tamaño (3mb máximo)
        $medida = 1000 * 3000;
        if($imagen['size'] > $medida){
            $errores[] = "La imagen es muy pesada";
        }
        
        if(empty($errores)){

            // Subida de archivos

            $carpetaImagenes = '../../imagenes/';
            // Crear la carpeta si no existe
            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes);
            }
            // Generar un nombre unico
            $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";

            // subir la imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);

            // Insertar en la base de datos
            $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, id_ven)
            VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', NOW(), '$vendedorID')";

            echo $query;

            $resultado = mysqli_query($db, $query);

            if($resultado){
                echo "Insertado Correctamente";
                header('Location: /admin/index.php?resultado=1');
            }
        }
    }



    require '../../includes/funciones.php';
    incluirTemplate('header');
 ?>
    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="../index.php" class="boton boton-verde">Volver</a>
        
        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>


        <form action="" class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name = "titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png" value="<?php echo $imagen; ?>">

                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion"><?php echo $descripcion; ?></textarea>
            </fieldset>
 
            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="precio">habitaciones:</label>
                <input 
                type="number" id="habitaciones" name="habitaciones"  placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">
    
                <label for="precio">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc; ?>">

                <label for="precio">Estacionamiento:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento; ?>">

            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select id = "vendedor" name = "vendedor">
                    <option value="">Seleccionar</option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                        <option <?php echo $vendedor['id_ven'] === $vendedorID ? 'selected' : ''; ?> value="<?php echo $vendedor['id_ven']; ?>">
                            <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>


    </main>

    

<?php 
    incluirTemplate('footer');
 ?>