<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    die("No hay ningún usuario iniciado.");
}


echo "Usuario conectado: "
    . htmlspecialchars($_SESSION["nombre_usuario"]);

echo "<br>";

echo "Email: "
    . htmlspecialchars($_SESSION["email"]);

echo "<br>";

echo "ID de usuario: "
    . htmlspecialchars($_SESSION["id_usuario"]);

echo "<br>";

echo "Rol: "
    . htmlspecialchars($_SESSION["rol"]);

?>

