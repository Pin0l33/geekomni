<?php
session_start();
require_once __DIR__ . "/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/login.html");
    exit;
}

$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["contrasena"] ?? "";

if ($usuario === "" || $password === "") {
    header("Location: ../html/login.html?error=datos");
    exit;
}

try {
    $consulta = $pdo->prepare(
        "SELECT id_usuario, nombre_usuario, email, password_hash
         FROM usuarios
         WHERE nombre_usuario = ? OR email = ?
         LIMIT 1"
    );
    $consulta->execute([$usuario, $usuario]);
    $cuenta = $consulta->fetch();

    if (!$cuenta || !password_verify($password, $cuenta["password_hash"])) {
        header("Location: ../html/login.html?error=credenciales");
        exit;
    }

    session_regenerate_id(true);
    $_SESSION["id_usuario"] = (int) $cuenta["id_usuario"];
    $_SESSION["nombre_usuario"] = $cuenta["nombre_usuario"];
    $_SESSION["email"] = $cuenta["email"];

    header("Location: ../html/index.html?login=ok");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    header("Location: ../html/login.html?error=servidor");
    exit;
}