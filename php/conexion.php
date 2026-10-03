<?php

$servidor = "localhost";
$baseDatos = "geek_omniverse";
$usuario = "root";
$contrasena = "";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    echo "Conexión exitosa con la base de datos.";

} catch (PDOException $error) {
    die("Error de conexión: " . $error->getMessage());
}

?>