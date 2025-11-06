<?php

// Importar la conexion
require 'includes/config/database.php';
$db = conectaDB();

// Crear un email y password
$email = "usuario@ejemplo.com";
$password = "123456";

$password = password_hash($password, PASSWORD_BCRYPT);

// Query para crear el usuario
$query = "INSERT INTO usuarios (email, password) VALUES ('$email', '$password')";

echo $query;

mysqli_query($db, $query);
?>