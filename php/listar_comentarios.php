<?php

require_once __DIR__ . "/conexion.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header("Content-Type: application/json; charset=utf-8");

$categoria = trim($_GET["categoria"] ?? "");

$categorias = [
    "juegos",
    "marvel",
    "dc",
    "anime_manga",
    "general"
];

try {

    if (
        $categoria !== "" &&
        !in_array($categoria, $categorias, true)
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Categoría no válida."
        ]);

        exit;
    }

    $sql = "SELECT
                c.id_comentario,
                c.categoria,
                c.cuerpo,
                c.fecha_creacion,
                u.nombre_usuario
            FROM comentarios c
            INNER JOIN usuarios u
                ON c.id_usuario = u.id_usuario";

    $params = [];

    if ($categoria !== "") {
        $sql .= " WHERE c.categoria = ?";
        $params[] = $categoria;
    }

    $sql .= " ORDER BY c.fecha_creacion DESC LIMIT 100";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(
        $stmt->fetchAll(),
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "error" => "No se pudieron cargar los comentarios."
    ]);
}