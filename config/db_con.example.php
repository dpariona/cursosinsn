<?php
//config/db_con.example.php
// Archivo de ejemplo para conexión a la base de datos
// Renombra este archivo como db_con.php y completa los datos reales

$host = 'localhost';
$user = 'root';
$password = ''; // ← tu contraseña aquí (NO en este archivo)
$dbname = 'nombre_de_tu_base';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa";
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
