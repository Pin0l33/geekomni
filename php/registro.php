<?php

require_once "conexion.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {



    $usuario = trim($_POST["usuario"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";




    if ($usuario === "" || $email === "" || $contrasena === "") {

        die("Todos los campos son obligatorios.");

    }




    if (strlen($contrasena) < 6) {

        die("La contraseña debe tener al menos 6 caracteres.");

    }


    

    $sql = "SELECT id_usuario
            FROM usuarios
            WHERE nombre_usuario = :usuario
            OR email = :email";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":usuario" => $usuario,
        ":email" => $email
    ]);


    if ($consulta->fetch()) {

        die("El nombre de usuario o email ya está registrado.");

    }


   

    $password_hash = password_hash(
        $contrasena,
        PASSWORD_DEFAULT
    );


    

    $sql = "INSERT INTO usuarios
            (nombre_usuario, email, password_hash)
            VALUES
            (:usuario, :email, :password_hash)";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":usuario" => $usuario,
        ":email" => $email,
        ":password_hash" => $password_hash
    ]);


    echo "Usuario registrado correctamente.";

}

?>
