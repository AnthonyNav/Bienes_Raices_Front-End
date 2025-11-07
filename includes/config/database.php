<?php

function conectaDB(): mysqli
{
    $host = getenv('DB_HOST') ?: 'mysql';
    $user = getenv('DB_USERNAME') ?: 'bienes_user';
    $pass = getenv('DB_PASSWORD') ?: 'bienes_pass';
    $name = getenv('DB_DATABASE') ?: 'bienes_raices';
    $port = getenv('DB_PORT') ? (int) getenv('DB_PORT') : 3306;

    $db = mysqli_connect($host, $user, $pass, $name, $port);

    if (!$db) {
        // Muestra el error real para depurar (puedes ocultarlo en producción)
        echo "Error: no se pudo conectar a la base de datos. Detalle: " . mysqli_connect_error();
        exit;
    }

    // Charset recomendado para emojis y acentos
    mysqli_set_charset($db, 'utf8mb4');

    return $db;
}
