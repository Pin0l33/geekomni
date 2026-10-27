<?php
require_once __DIR__ . "/conexion.php";

header("Content-Type: application/json; charset=utf-8");

$id_contenido = filter_input(
    INPUT_GET,
    "id_contenido",
    FILTER_VALIDATE_INT
);
$categoria = trim($_GET["categoria"] ?? "");

if (!$id_contenido || $id_contenido < 1 || $categoria === "") {
    http_response_code(400);
    echo json_encode(["error" => "Falta indicar el contenido y la categoría."]);
    exit;
}

try {
    $sql = "SELECT c.id_comentario, c.id_usuario,
                   c.id_contenido, c.categoria, c.cuerpo,
                   c.fecha_creacion, u.nombre_usuario
            FROM comentarios c
            INNER JOIN usuarios u
                ON c.id_usuario = u.id_usuario
            WHERE c.id_contenido = :contenido
              AND c.categoria = :categoria
            ORDER BY c.fecha_creacion DESC";

    $consulta = $pdo->prepare($sql);
    $consulta->execute([
        ":contenido" => $id_contenido,
        ":categoria" => $categoria
    ]);

    echo json_encode(
        $consulta->fetchAll(),
        JSON_UNESCAPED_UNICODE
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error" => "No se pudieron cargar los comentarios."
    ]);
}