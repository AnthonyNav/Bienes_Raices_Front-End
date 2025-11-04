<?php 
    // Extraer el id de la propiedad
    $id = $_GET['id'];
    if( !filter_var($id, FILTER_VALIDATE_INT) ) {
        die('No es válido');
    }

    require 'includes/config/database.php';
    $db = conectaDB();

    // Obtener los datos de la propiedad
    $query = "SELECT * FROM propiedades WHERE id_prop = $id";
    $resultado = mysqli_query($db, $query);

    if(!$resultado->num_rows) {
        header('Location: index.php');
    }

    $propiedad = mysqli_fetch_assoc($resultado);
    //var_dump($propiedad);

    require 'includes/funciones.php';
    incluirTemplate('header');
 ?>

    <main class="contenedor seccion contenido-centrado">
        <h1><?php echo $propiedad['titulo']; ?></h1>

        <img loading="lazy" src="imagenes/<?php echo $propiedad['imagen']; ?>" alt="anuncio">

        <div class="resumen-propiedad">
            <p class="precio"><?php echo $propiedad['precio']; ?></p>
            <ul class="iconos-caracteristicas">
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="icono wc">
                    <p><?php echo $propiedad['wc']; ?></p>
                </li>
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                    <p><?php echo $propiedad['estacionamiento']; ?></p>
                </li>
                <li>
                    <img class="icono"  loading="lazy" src="build/img/icono_dormitorio.svg" alt="icono habitaciones">
                    <p><?php echo $propiedad['habitaciones']; ?></p>
                </li>
            </ul>

            <p>
                <?php echo $propiedad['descripcion']; ?>
            </p>
        </div>
    </main>

<?php 
    incluirTemplate('footer');
    // Cerrar la conexion
    mysqli_close($db);
 ?>