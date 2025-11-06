<?php 
    // Importar el template
    require '../includes/funciones.php';
    if(!estaAutenticado()){
        header('Location: /');
    }
    // echo "<pre>";
    // var_dump(($_POST));
    // echo "</pre>";
    // Importar la conexion
    require '../includes/config/database.php';
    $db = conectaDB();

    // Escribir el Query
    $query = "SELECT * FROM propiedades";
    // Consultar la BD
    $resultadoConsulta = mysqli_query($db, $query);



    // Muestra un mensaje condicional
    $resultado = $_GET['resultado'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar ID
        $id = $_POST['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if($id){
            // Eliminar el archivo
            $consultaImagen = "SELECT imagen FROM propiedades WHERE id_prop = $id";
            $resultadoImagen = mysqli_query($db, $consultaImagen);
            $propiedad = mysqli_fetch_assoc($resultadoImagen);

            unlink('../imagenes/' . $propiedad['imagen']);

            // Eliminar la propiedad
            $consulta = "DELETE FROM propiedades WHERE id_prop = $id";
            $resultado = mysqli_query($db, $consulta);
            if($resultado){
                header('Location: /admin/index.php?resultado=3');
            }
        }
    }

    
    incluirTemplate('header');


 ?>
    <main class="contenedor seccion">
        <h1>Administrador de bienes Raices</h1>
        <?php if(intval($resultado) === 1): ?>
            <p class="alerta exito">Anuncio Creado Correctamente</p>
        <?php elseif(intval($resultado) === 2): ?>
            <p class="alerta exito">Anuncio Actualizado Correctamente</p>
        <?php elseif(intval($resultado) === 3): ?>
            <p class="alerta exito">Anuncio Eliminado Correctamente</p>
        <?php endif; ?>
        <a href="/admin/propiedades/crear.php" class="boton boton-verde">Crear Propiedad</a>
        <!-- <a href="/admin/propiedades/borrar.php" class="boton boton-verde">Borrar Propiedad</a>
        <a href="/admin/propiedades/actualizar.php" class="boton boton-verde">Actualizar Propiedad</a> -->

        <table class="propiedades">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titulo</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>

                <tbody>
                    <?php while($propiedad = mysqli_fetch_assoc($resultadoConsulta)): ?>
                    <tr>
                        <td><?php echo $propiedad['id_prop']; ?></td>
                        <td><?php echo $propiedad['titulo']; ?></td>
                        <td><?php echo $propiedad['precio']; ?></td>
                        <td><img src="/imagenes/<?php echo $propiedad['imagen']; ?>" class="imagen-tabla" alt="<?php echo $propiedad['titulo']; ?>"></td>
                        <td>
                            <form method="POST" class="">
                                <input type="hidden" name="id" value="<?php echo $propiedad['id_prop']; ?>">

                                <input type="submit" class="boton-rojo-block w-100" value="Eliminar">
                            </form>
                            <a href="/admin/propiedades/actualizar.php?id=<?php echo $propiedad['id_prop']; ?>" class="boton-verde-block">Actualizar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </thead>
        </table>



    </main>

   

<?php 
    // Cerrar la conexion
    mysqli_close($db);
    incluirTemplate('footer');
 ?>