<?php

// Importar la conexion
require 'includes/config/database.php';
$db = conectaDB();

// Crear un email y password
$nombre = "Juan";
$apellido = "Perez";
$telefono = "5551234567";

$nombre2 = "Maria";
$apellido2 = "Gomez";
$telefono2 = "5559876543";

$nombre3 = "Luis";
$apellido3 = "Lopez";
$telefono3 = "5556789123";

// Query para crear a los vendedores
$query = "INSERT INTO vendedores (nombre, apellido, telefono) VALUES
         ('$nombre', '$apellido', '$telefono'),
         ('$nombre2', '$apellido2', '$telefono2'),
         ('$nombre3', '$apellido3', '$telefono3')";

echo $query;

mysqli_query($db, $query);
?>