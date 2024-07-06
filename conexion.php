<?php

    include 'configuracion.php';
    $conexion = new mysqli($server, $user, $pass, $database, $port);
    if($conexion->connect_error) {
        die("Error al conectar a la base de datos" . $mysqli->connect_error);
        exit();
    }

?>