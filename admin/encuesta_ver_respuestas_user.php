<?php
include 'db_con.php';
include("sesion.class.php");

$sesion = new sesion();
$cargo = $sesion->get("cargo");
$ci = $sesion->get("usuario");

if ($cargo != '3') {
    echo "⛔ Acceso restringido.";
    exit;
}

if (!isset($_GET['encuesta_id'])) {
    echo "❗ Encuesta no especificada.";
    exit;
}

$id_encuesta = intval($_GET['encuesta_id']);
$curso_id = isset($_GET['curso_id']) ? intval($_GET['curso_id']) : 0;

// Obtener ID del usuario
$id_usuario = 0;
$res = $db_con->query("SELECT id FROM dat_admin WHERE ci = '$ci' LIMIT 1");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $id_usuario = $row['id'];
} else {
    echo "🚫 Usuario no encontrado.";
    exit;
}

// Verificar si respondió la encuesta
$verifica = $db_con->query("
    SELECT 1 FROM encuesta_respuestas
    WHERE id_usuario = $id_usuario AND id_encuesta = $id_encuesta
    LIMIT 1
");

if ($verifica->num_rows == 0) {
    echo "<div class='alert alert-warning'>⚠️ Aún no has respondido esta encuesta.</div>";
    exit;
}

// Obtener título de la encuesta
$info = $db_con->query("SELECT titulo_encuesta FROM encuestas WHERE id = $id_encuesta");
$titulo = ($info && $info->num_rows > 0) ? $info->fetch_assoc()['titulo_encuesta'] : "Encuesta";

// Obtener preguntas y respuestas del usuario
$preguntas = $db_con->query("
    SELECT p.txt_pregunta, o.txt_opcion
    FROM encuesta_respuestas r
    JOIN encuesta_preguntas p ON r.id_pregunta = p.id
    JOIN encuesta_opciones_respuesta o ON r.id_opcion = o.id
    WHERE r.id_encuesta = $id_encuesta AND r.id_usuario = $id_usuario
");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Respuestas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">📝 Respuestas de la encuesta: <?= htmlspecialchars($titulo) ?></h4>
        </div>
        <div class="card-body">
            <?php if ($preguntas && $preguntas->num_rows > 0): ?>
                <?php while ($row = $preguntas->fetch_assoc()): ?>
                    <div class="mb-4">
                        <p class="fw-bold"><?= htmlspecialchars($row['txt_pregunta']) ?></p>
                        <p class="text-success"><?= htmlspecialchars($row['txt_opcion']) ?></p>
                        <hr>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-warning">❗ No se encontraron respuestas.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
