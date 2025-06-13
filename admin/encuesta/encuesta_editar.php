<?php
include '../config/db_con.php';
include("../sesion.class.php");

$sesion = new sesion();
$cargo = $sesion->get("cargo");
$ci = $sesion->get("usuario");

if ($cargo != '4') {
    echo "⛔ Acceso denegado.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "⚠️ Encuesta no especificada.";
    exit;
}

$encuesta_id = intval($_GET['id']);

// Validar autor
$encuesta = $db_con->query("SELECT e.*, c.titulo_curso FROM encuestas e JOIN cursos c ON e.id_curso = c.id WHERE e.id = $encuesta_id AND e.autor_ci = '$ci'");
if ($encuesta->num_rows == 0) {
    echo "🚫 Encuesta no encontrada o sin permisos.";
    exit;
}
$encuesta = $encuesta->fetch_assoc();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar título
    $titulo_encuesta = $db_con->real_escape_string($_POST['titulo_encuesta']);
    $db_con->query("UPDATE encuestas SET titulo_encuesta = '$titulo_encuesta' WHERE id = $encuesta_id");

    // Eliminar preguntas
    if (!empty($_POST['eliminar_pregunta'])) {
        foreach ($_POST['eliminar_pregunta'] as $id) {
            $id = intval($id);
            $db_con->query("DELETE FROM encuesta_opciones_respuesta WHERE id_pregunta = $id");
            $db_con->query("DELETE FROM encuesta_preguntas WHERE id = $id");
        }
    }

    // Eliminar opciones
    if (!empty($_POST['eliminar_opcion'])) {
        foreach ($_POST['eliminar_opcion'] as $id) {
            $id = intval($id);
            $db_con->query("DELETE FROM encuesta_opciones_respuesta WHERE id = $id");
        }
    }

    // Editar preguntas
    if (!empty($_POST['preguntas'])) {
        foreach ($_POST['preguntas'] as $id => $txt_pregunta) {
            $id = intval($id);
            $txt_pregunta = $db_con->real_escape_string($txt_pregunta);
            $db_con->query("UPDATE encuesta_preguntas SET txt_pregunta = '$txt_pregunta' WHERE id = $id");
        }
    }

    // Editar opciones existentes
    if (!empty($_POST['opciones'])) {
        foreach ($_POST['opciones'] as $id => $txt_opcion) {
            $id = intval($id);
            $txt_opcion = $db_con->real_escape_string($txt_opcion);
            $db_con->query("UPDATE encuesta_opciones_respuesta SET txt_opcion = '$txt_opcion' WHERE id = $id");
        }
    }

    // Nuevas opciones para preguntas existentes
    if (!empty($_POST['nuevas_opciones_existentes'])) {
        foreach ($_POST['nuevas_opciones_existentes'] as $id_pregunta => $opciones) {
            $id_pregunta = intval($id_pregunta);
            foreach ($opciones as $texto_op) {
                $texto_op = $db_con->real_escape_string($texto_op);
                if ($texto_op !== "") {
                    $db_con->query("INSERT INTO encuesta_opciones_respuesta (id_pregunta, txt_opcion) VALUES ($id_pregunta, '$texto_op')");
                }
            }
        }
    }

    // Nuevas preguntas y sus opciones
    if (!empty($_POST['nueva_pregunta'])) {
        foreach ($_POST['nueva_pregunta'] as $key => $txt_pregunta) {
            $txt_pregunta = $db_con->real_escape_string($txt_pregunta);
            $db_con->query("INSERT INTO encuesta_preguntas (id_encuesta, txt_pregunta) VALUES ($encuesta_id, '$txt_pregunta')");
            $id_nueva = $db_con->insert_id;

            if (!empty($_POST['nuevas_opciones'][$key])) {
                foreach ($_POST['nuevas_opciones'][$key] as $opcion) {
                    $opcion = $db_con->real_escape_string($opcion);
                    if ($opcion != "") {
                        $db_con->query("INSERT INTO encuesta_opciones_respuesta (id_pregunta, txt_opcion) VALUES ($id_nueva, '$opcion')");
                    }
                }
            }
        }
    }

    header("Location: encuesta_editar.php?id=$encuesta_id&guardado=1");
    exit;
}

// Obtener preguntas y opciones
$preguntas = $db_con->query("SELECT * FROM encuesta_preguntas WHERE id_encuesta = $encuesta_id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Encuesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .opcion-input { margin-bottom: 0.5rem; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">✏️ Editar Encuesta</h4>
            <a href="encuesta_admin_lista.php" class="btn btn-outline-light btn-sm">⬅ Volver</a>
        </div>
        <div class="card-body">
            <?php if (isset($_GET['guardado'])): ?>
                <div class="alert alert-success">✅ Cambios guardados correctamente.</div>
            <?php endif; ?>
            <form method="post" id="formEncuesta">
                <div class="mb-3">
                    <label class="form-label">Título de la Encuesta</label>
                    <input type="text" name="titulo_encuesta" class="form-control" required value="<?= htmlspecialchars($encuesta['titulo_encuesta']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Curso asociado</label>
                    <input type="text" class="form-control" readonly value="<?= htmlspecialchars($encuesta['titulo_curso']) ?>">
                </div>

                <hr>
                <h5>Preguntas y Opciones</h5>

                <?php while ($p = $preguntas->fetch_assoc()): ?>
                    <div class="mb-4 border rounded p-3 bg-white pregunta-bloque">
                        <div class="d-flex justify-content-between">
                            <label><strong>Pregunta:</strong></label>
                            <label class="text-danger"><input type="checkbox" name="eliminar_pregunta[]" value="<?= $p['id'] ?>"> Eliminar</label>
                        </div>
                        <input type="text" name="preguntas[<?= $p['id'] ?>]" class="form-control mb-2" value="<?= htmlspecialchars($p['txt_pregunta']) ?>">

                        <div class="opciones" data-pregunta-id="<?= $p['id'] ?>">
                            <?php
                            $opciones = $db_con->query("SELECT * FROM encuesta_opciones_respuesta WHERE id_pregunta = {$p['id']}");
                            while ($op = $opciones->fetch_assoc()):
                            ?>
                                <div class="input-group opcion-input">
                                    <input type="text" name="opciones[<?= $op['id'] ?>]" class="form-control" value="<?= htmlspecialchars($op['txt_opcion']) ?>">
                                    <div class="input-group-text bg-light">
                                        <input type="checkbox" name="eliminar_opcion[]" value="<?= $op['id'] ?>"> ❌
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 agregar-opcion" data-pregunta-id="<?= $p['id'] ?>">➕ Más opciones</button>
                    </div>
                <?php endwhile; ?>

                <hr>
                <h5>Agregar Nueva Pregunta</h5>
                <div id="preguntas-nuevas"></div>

                <button type="button" class="btn btn-outline-success mb-3" id="agregar-pregunta">➕ Otra Pregunta</button>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">💾 Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let preguntaNuevaID = 0;

$("#agregar-pregunta").click(function() {
    preguntaNuevaID++;
    const bloque = `
    <div class="mb-4 border rounded p-3 bg-white pregunta-nueva">
        <label><strong>Nueva Pregunta:</strong></label>
        <input type="text" name="nueva_pregunta[${preguntaNuevaID}]" class="form-control mb-2" required>

        <div class="opciones-nuevas" data-nueva-id="${preguntaNuevaID}">
            <input type="text" name="nuevas_opciones[${preguntaNuevaID}][]" class="form-control mb-1" placeholder="Opción 1">
        </div>

        <button type="button" class="btn btn-sm btn-outline-secondary agregar-opcion-nueva" data-nueva-id="${preguntaNuevaID}">➕ Más opciones</button>
    </div>`;
    $("#preguntas-nuevas").append(bloque);
});

$(document).on("click", ".agregar-opcion", function() {
    const id = $(this).data("pregunta-id");
    const input = `<input type="text" name="nuevas_opciones_existentes[${id}][]" class="form-control mb-1" placeholder="Nueva opción...">`;
    $(this).siblings(".opciones").append(input);
});

$(document).on("click", ".agregar-opcion-nueva", function() {
    const nuevaID = $(this).data("nueva-id");
    const input = `<input type="text" name="nuevas_opciones[${nuevaID}][]" class="form-control mb-1" placeholder="Otra opción">`;
    $(`.opciones-nuevas[data-nueva-id="${nuevaID}"]`).append(input);
});
</script>
</body>
</html>
