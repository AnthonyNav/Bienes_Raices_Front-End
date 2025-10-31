<?php 
    // Conexion a la base de datos
    require '../../includes/config/database.php';
    $db = conectaDB();
    var_dump($db);


    require '../../includes/funciones.php';
    incluirTemplate('header');
 ?>
    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="../index.php" class="boton boton-verde">Volver</a>

        <form action="" class="formulario">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" placeholder="Titulo Propiedad">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" placeholder="Precio Propiedad">

                <label for="titulo">Titulo:</label>
                <input type="file" id="Imagen" accept="image/jpeg, image/png">

                <label for="descripcion">Descripción:</label>
                <textarea name="" id="descripcion"></textarea>
            </fieldset>
 
            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="precio">habitaciones:</label>
                <input type="number" id="habitaciones" placeholder="Ej: 3" min="1" max="9">
    
                <label for="precio">Baños:</label>
                <input type="number" id="habitaciones" placeholder="Ej: 3" min="1" max="9">

                <label for="precio">Estacionamiento:</label>
                <input type="number" id="habitaciones" placeholder="Ej: 3" min="1" max="9">

            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select>
                    <option value="1">Juan</option>
                    <option value="2">Karen</option>
                </select>
            </fieldset>

            <input type="submit" class="boton boton-verde">
        </form>


    </main>

    

<?php 
    incluirTemplate('footer');
 ?>