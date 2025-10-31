<?php

function conectaDB():mysqli{
    $db = mysqli_connect('localhost','root', '', 'bienes_raices', 3306);

    if(!$db) {
        echo "Error no se pudo conectar";
        exit;
    } 

    return $db; 
}