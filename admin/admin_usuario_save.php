<?php
include '../config/db_con.php';

$ci = $_POST['ci'];
$username = $_POST['username'];
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
$rol = $_POST['rol'];
$estado = $_POST['estado'];

// Carpeta destino
$carpeta = "../assets/uploads/usuarios/";
if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Procesar foto
$foto_nombre = null;
if (!empty($_FILES['foto']['name'])) {
    $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    $foto_nombre = $ci . "." . $extension;  // Usamos el CI como nombre de archivo
    $ruta_destino = $carpeta . $foto_nombre;

    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta_destino)) {
        echo "Error al subir la imagen.";
        exit;
    }
}

$sql = "INSERT INTO usuarios (ci, username, nombre, correo, clave, rol, estado, foto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $db_con->prepare($sql);
$stmt->bind_param("ssssssss", $ci, $username, $nombre, $correo, $clave, $rol, $estado, $foto_nombre);

if ($stmt->execute()) {
    echo "Usuario registrado correctamente. <a href='registro_usuario_lista.php'>Ver lista</a>";
} else {
    echo "Error al registrar: " . $db_con->error;
}
?>
