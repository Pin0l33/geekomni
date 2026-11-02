<?php

session_start();

require_once __DIR__ . "/conexion.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "error" => "Método no permitido."
    ]);

    exit;
}

if (!isset($_SESSION["id_usuario"])) {

    http_response_code(401);

    echo json_encode([
        "error" => "Iniciá sesión para comentar."
    ]);

    exit;
}

$cuerpo = trim($_POST["cuerpo"] ?? "");
$categoria = trim($_POST["categoria"] ?? "");

$permitidas = [
    "juegos",
    "marvel",
    "dc",
    "anime_manga",
    "general"
];

if (
    $cuerpo === "" ||
    mb_strlen($cuerpo) > 1000 ||
    !in_array($categoria, $permitidas, true)
) {

    http_response_code(400);

    echo json_encode([
        "error" => "Revisá el comentario y la categoría."
    ]);

    exit;
}

try {

    $stmt = $pdo->prepare(
        "INSERT INTO comentarios
            (id_usuario, id_contenido, categoria, cuerpo)
         VALUES (?, NULL, ?, ?)"
    );

    $stmt->execute([
        $_SESSION["id_usuario"],
        $categoria,
        $cuerpo
    ]);

    echo json_encode([
        "ok" => true
    ]);

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "error" => "No se pudo guardar el comentario."
    ]);
}