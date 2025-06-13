<?php
//admin/encuesta/encuesta_nueva.php
date_default_timezone_set('America/Lima');
$fecha_actual = date("Y-m-d H:i:s");

//require_once  '../../templates/header_admin.php'; // Ya incluye config y control de sesión
//define('APP_RUNNING', true);
//require_once dirname(__DIR__, 2) . '/config/config.php'; // <-- muy importante
//require_once dirname(__DIR__, 2) . '/templates/header_admin.php';
//require_once  '../../templates/sidebar_admin.php';
//require_once '../../templates/header_admin.php';
//define('APP_RUNNING', true);
require_once dirname(__DIR__, 2) . '/templates/header_admin.php';
require_once dirname(__DIR__, 2) . '/templates/sidebar_admin.php';

/*
$sesion = new sesion();
$cargo = $sesion->get("cargo");
$usuario = $sesion->get("usuario");

if ($cargo != '4') {
    echo "<div class='alert alert-danger'>Acceso denegado. No tienes permiso para crear encuestas.</div>";
    exit;
}*/

$cursos = $db_con->query("SELECT id, titulo_curso FROM cursos WHERE estado='nuevo' ORDER BY titulo_curso");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_curso = $_POST['id_curso'];
    $titulo_encuesta = trim($_POST['titulo_encuesta']);
    $preguntas = $_POST['preguntas'] ?? [];
    $opciones = $_POST['opciones'] ?? [];

    if ($titulo_encuesta === '') {
        echo "<div class='alert alert-danger'>⚠️ Debes ingresar un título para la encuesta.</div>";
    } else {
        $stmt = $db_con->prepare("INSERT INTO encuestas (id_curso, titulo_encuesta, autor_ci, fecha_creacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_curso, $titulo_encuesta, $usuario, $fecha_actual);
        $stmt->execute();
        $id_encuesta = $stmt->insert_id;

        foreach ($preguntas as $i => $txt_pregunta) {
            $txt_pregunta = trim($txt_pregunta);
            if ($txt_pregunta === '') continue;

            $stmt2 = $db_con->prepare("INSERT INTO encuesta_preguntas (id_encuesta, txt_pregunta) VALUES (?, ?)");
            $stmt2->bind_param("is", $id_encuesta, $txt_pregunta);
            $stmt2->execute();
            $id_pregunta = $stmt2->insert_id;

            if (isset($opciones[$i]) && is_array($opciones[$i])) {
                foreach ($opciones[$i] as $opcion) {
                    $opcion = trim($opcion);
                    if ($opcion === '') continue;

                    $stmt3 = $db_con->prepare("INSERT INTO encuesta_opciones_respuesta (id_pregunta, txt_opcion) VALUES (?, ?)");
                    $stmt3->bind_param("is", $id_pregunta, $opcion);
                    $stmt3->execute();
                }
            }
        }

        echo '<div class="alert alert-success mt-3">✅ Encuesta creada correctamente.</div>';
    }
}
?>
<main>

<div class="container py-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📝 Crear Encuesta para Curso</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="titulo_encuesta" class="form-label fw-bold">📝 Título de la encuesta:</label>
                    <input type="text" name="titulo_encuesta" id="titulo_encuesta" class="form-control" required placeholder="Ej: Encuesta de satisfacción">
                </div>

                <div class="mb-3">
                    <label for="id_curso" class="form-label fw-bold">📚 Selecciona un curso:</label>
                    <select name="id_curso" id="id_curso" class="form-select" required>
                        <option value="">-- Selecciona --</option>
                        <?php while ($c = $cursos->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['titulo_curso']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <hr class="my-4">

                <div id="preguntas-container">
                    <div class="mb-4 pregunta border rounded p-3 bg-light-subtle">
                        <label class="form-label">❓ Pregunta:</label>
                        <input type="text" name="preguntas[0]" class="form-control mb-3" required placeholder="Ej: ¿Qué esperas del curso?">

                        <label class="form-label">✅ Opciones de respuesta:</label>
                        <div class="opciones-container mb-3">
                            <input type="text" name="opciones[0][]" class="form-control mb-2" placeholder="Opción 1" required>
                            <input type="text" name="opciones[0][]" class="form-control mb-2" placeholder="Opción 2" required>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarOpcion(this, 0)">➕ Agregar opción</button>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4">
                    <button type="button" class="btn btn-outline-primary" onclick="agregarPregunta()">➕ Agregar otra pregunta</button>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">💾 Guardar Encuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let preguntaIndex = 1;

function agregarPregunta() {
    const container = document.getElementById('preguntas-container');
    const div = document.createElement('div');
    div.className = 'mb-4 pregunta border rounded p-3 bg-light-subtle';
    div.innerHTML = `
        <label class="form-label">❓ Pregunta:</label>
        <input type="text" name="preguntas[${preguntaIndex}]" class="form-control mb-3" required placeholder="Ej: ¿Qué te motivó a inscribirte?">

        <label class="form-label">✅ Opciones de respuesta:</label>
        <div class="opciones-container mb-3">
            <input type="text" name="opciones[${preguntaIndex}][]" class="form-control mb-2" placeholder="Opción 1" required>
            <input type="text" name="opciones[${preguntaIndex}][]" class="form-control mb-2" placeholder="Opción 2" required>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarOpcion(this, ${preguntaIndex})">➕ Agregar opción</button>
    `;
    container.appendChild(div);
    preguntaIndex++;
}

function agregarOpcion(button, index) {
    const opcionesContainer = button.parentElement.querySelector('.opciones-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = `opciones[${index}][]`;
    input.className = 'form-control mb-2';
    input.placeholder = 'Otra opción';
    opcionesContainer.appendChild(input);
}
</script>

</main>
<?php //require_once BASE_PATH . '/templates/footer_admin.php'; 
require_once __DIR__ . '/../../templates/footer_admin.php';
?>