<?php
session_start();
require_once __DIR__ . "/conexion.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    echo json_encode(["error" => "Iniciá sesión para comentar."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido."]);
    exit;
}

$cuerpo = trim($_POST["cuerpo"] ?? "");
$id_contenido = filter_input(
    INPUT_POST,
    "id_contenido",
    FILTER_VALIDATE_INT
);
$categoria = trim($_POST["categoria"] ?? "");

if (
    $cuerpo === "" ||
    mb_strlen($cuerpo) > 1000 ||
    !$id_contenido ||
    $id_contenido < 1 ||
    $categoria === ""
) {
    http_response_code(400);
    echo json_encode([
        "error" => "Completá el comentario, el contenido y la categoría."
    ]);
    exit;
}

try {
    $sql = "INSERT INTO comentarios
            (id_usuario, id_contenido, categoria, cuerpo)
            VALUES (:usuario, :contenido, :categoria, :cuerpo)";

    $consulta = $pdo->prepare($sql);
    $consulta->execute([
        ":usuario" => $_SESSION["id_usuario"],
        ":contenido" => $id_contenido,
        ":categoria" => $categoria,
        ":cuerpo" => $cuerpo
    ]);

    echo json_encode(["ok" => true]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error" => "No se pudo guardar el comentario."
    ]);
}