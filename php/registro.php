<?php
session_start();
require_once __DIR__ . "/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/registro.html");
    exit;
}

$nombre = trim($_POST["usuario"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["contrasena"] ?? "";

if (
    $nombre === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($password) < 8 ||
    strlen($nombre) > 50
) {
    header("Location: ../html/registro.html?error=datos");
    exit;
}

try {
    $consulta = $pdo->prepare(
        "SELECT id_usuario FROM usuarios
         WHERE nombre_usuario = ? OR email = ? LIMIT 1"
    );
    $consulta->execute([$nombre, $email]);

    if ($consulta->fetch()) {
        header("Location: ../html/registro.html?error=existe");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $insertar = $pdo->prepare(
        "INSERT INTO usuarios (nombre_usuario, email, password_hash)
         VALUES (?, ?, ?)"
    );
    $insertar->execute([$nombre, $email, $hash]);

    session_regenerate_id(true);
    $_SESSION["id_usuario"] = (int) $pdo->lastInsertId();
    $_SESSION["nombre_usuario"] = $nombre;
    $_SESSION["email"] = $email;

    header("Location: ../html/index.html?registro=ok");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    header("Location: ../html/registro.html?error=servidor");
    exit;
}