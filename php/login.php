<?php

session_start();

require_once "conexion.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acceso no permitido.");
}


$usuario = trim($_POST["usuario"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";


if ($usuario === "" || $contrasena === "") {
    die("Completá todos los campos.");
}


$sql = "SELECT id_usuario, nombre_usuario, email, password_hash, rol
        FROM usuarios
        WHERE nombre_usuario = :usuario
        OR email = :usuario
        LIMIT 1";

$consulta = $conexion->prepare($sql);

$consulta->execute([
    ":usuario" => $usuario
]);


$usuarioEncontrado = $consulta->fetch(PDO::FETCH_ASSOC);


if (!$usuarioEncontrado) {
    die("El usuario o email no existe.");
}


if (!password_verify(
    $contrasena,
    $usuarioEncontrado["password_hash"]
)) {
    die("La contraseña es incorrecta.");
}


$_SESSION["id_usuario"] = $usuarioEncontrado["id_usuario"];
$_SESSION["nombre_usuario"] = $usuarioEncontrado["nombre_usuario"];
$_SESSION["email"] = $usuarioEncontrado["email"];
$_SESSION["rol"] = $usuarioEncontrado["rol"];



echo "Inicio de sesión exitoso. Bienvenido "
    . htmlspecialchars($usuarioEncontrado["nombre_usuario"]);

?>
