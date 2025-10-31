<?php 
    require '../includes/funciones.php';
    incluirTemplate('header');
 ?>
    <main class="contenedor seccion">
        <h1>Administrador de bienes Raices</h1>

        <a href="propiedades/crear.php" class="boton boton-verde">Crear Propiedad</a>
        <a href="propiedades/borrar.php" class="boton boton-verde">Borrar Propiedad</a>
        <a href="propiedades/actualizar.php" class="boton boton-verde">Actualizar Propiedad</a>
    </main>

   

<?php 
    incluirTemplate('footer');
 ?>