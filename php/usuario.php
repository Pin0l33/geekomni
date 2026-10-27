<?php
session_start();

header("Content-Type: application/json; charset=utf-8");

if (isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "logueado" => true,
        "id_usuario" => $_SESSION["id_usuario"],
        "nombre_usuario" => $_SESSION["nombre_usuario"],
        "email" => $_SESSION["email"] ?? ""
    ]);
} else {
    echo json_encode([
        "logueado" => false
    ]);
}