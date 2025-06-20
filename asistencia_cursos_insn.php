<?php
//asistencia_cursos_insn.php
// Mostrar errores detalladamente
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('America/Lima');
include 'config/db_con.php';
session_start();

$mensaje = '';
$ci = $_POST['ci'] ?? $_GET['ci'] ?? null;
$id_curso = $_POST['id_curso'] ?? $_GET['curso_id'] ?? null;
$marcar_asistencia = isset($_POST['marcar_asistencia']); // importante

$curso = null;
if ($id_curso) {
    $stmt = $db_con->prepare("SELECT * FROM cursos WHERE id = ?");
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $curso = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body class="container mt-5">
<div class="card">
    <div class="card-header bg-primary text-white text-center">
        <h4 class="mb-1">Registro de Asistencia</h4>
        <h6 class="mb-0">Curso: <?= htmlspecialchars($curso['titulo_curso'] ?? 'Curso') ?></h6>
    </div>
    <div class="card-body">
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <div class="col-12 col-md-4">
                <label for="tipo_doc" class="form-label">Tipo de Documento</label>
                <select name="tipo_doc" id="tipo_doc" class="form-select" required>
                    <option value="">Seleccione...</option>
                    <option value="DNI">DNI (8 dígitos)</option>
                    <option value="C.E">C.E / C.I. (hasta 18 caracteres)</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label for="ci" class="form-label">DNI/CI</label>
                <input type="text" name="ci" id="ci" class="form-control" value="<?= htmlspecialchars($ci) ?>" autocomplete="off" onkeyup="this.value = this.value.toUpperCase();" required>
                <small id="ciHelp" class="form-text text-muted">Ingrese su número de documento.</small>
            </div>
            <input type="hidden" name="id_curso" value="<?= htmlspecialchars($id_curso) ?>">
            <div class="col-12 col-md-4 d-flex align-items-end justify-content-start justify-content-md-end">
                <button type="submit" name="buscar" class="btn btn-primary w-100 w-md-auto">Buscar</button>
            </div>
        </form>

        <?php
        if ($ci && $id_curso) {
            $stmt = $db_con->prepare("SELECT * FROM dat_admin WHERE ci = ?");
            $stmt->bind_param("s", $ci);
            $stmt->execute();
            $est = $stmt->get_result()->fetch_assoc();

            if ($est) {
                $id_datos = $est['id'];

                $stmt = $db_con->prepare("SELECT * FROM inscripciones WHERE id_curso = ? AND id_datos = ?");
                $stmt->bind_param("ii", $id_curso, $id_datos);
                $stmt->execute();
                $res_insc = $stmt->get_result();

                if ($res_insc->num_rows > 0): ?>
                    <hr>
                    <div class="alert alert-success text-center">
                        Bienvenido, <strong><?= htmlspecialchars($est['nom']) ?></strong>
                    </div>
<form id="form-asistencia" class="text-center">
    <input type="hidden" name="ci" value="<?= htmlspecialchars($ci) ?>">
    <input type="hidden" name="id_curso" value="<?= htmlspecialchars($id_curso) ?>">
    <button type="button" id="btnEntrada" class="btn btn-success me-2">✅ Marcar Entrada</button>
    <button type="button" id="btnSalida" class="btn btn-danger">🔁 Marcar Salida</button>
</form>
<div id="mensaje-asistencia" class="mt-3"></div>

                <?php else: ?>
                    <div class="alert alert-warning mt-3 text-center">⚠ <?= htmlspecialchars($est['nom']) ?>, no estás inscrito en este curso.</div>
                <?php endif;
            } else {
                echo '<div class="alert alert-warning mt-3 text-center">⚠ Usuario no registrado.</div>';
            }
        }
        ?>
    </div>
</div>


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoDoc = document.getElementById('tipo_doc');
        const ciInput = document.getElementById('ci');
        const ciHelp = document.getElementById('ciHelp');

        tipoDoc.addEventListener('change', function () {
            ciInput.value = '';
            ciInput.setCustomValidity('');
            ciInput.removeAttribute('pattern');

            if (this.value === 'DNI') {
                ciInput.placeholder = 'Ej: 12345678';
                ciInput.maxLength = 8;
                ciHelp.textContent = 'DNI debe contener 8 dígitos numéricos.';
            } else if (this.value === 'C.E') {
                ciInput.placeholder = 'Ej: 12345678-BR';
                ciInput.maxLength = 18;
                ciHelp.textContent = 'CI/CE Hasta 18 caracteres (Ej: 123456789012-BR).';
            } else {
                ciHelp.textContent = 'Ingrese su número de documento.';
            }
        });

        ciInput.addEventListener('input', function () {
            const tipo = tipoDoc.value;
            const value = ciInput.value.trim();

            if (tipo === 'DNI') {
                ciInput.setCustomValidity(/^\d{8}$/.test(value) ? '' : 'El DNI debe tener 8 dígitos numéricos.');
            } else if (tipo === 'C.E') {
                ciInput.setCustomValidity(/^[\w-]{1,18}$/.test(value) ? '' : 'Máximo 18 caracteres alfanuméricos.');
            } else {
                ciInput.setCustomValidity('Seleccione el tipo de documento.');
            }
        });
    });
    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnEntrada = document.getElementById('btnEntrada');
    const btnSalida = document.getElementById('btnSalida');
    const ci = document.querySelector('[name="ci"]').value;
    const id_curso = document.querySelector('[name="id_curso"]').value;
    const mensajeDiv = document.getElementById('mensaje-asistencia');

    function enviarAsistencia(tipo) {
        const datos = new FormData();
        datos.append('ci', ci);
        datos.append('id_curso', id_curso);
        if (tipo === 'salida') datos.append('marcar_salida', '1');

        fetch(tipo === 'entrada' ? 'asistencia_ajax.php' : 'asistencia_salida_ajax.php', {
            method: 'POST',
            body: datos
        })
        .then(res => res.json())
        .then(data => {
            mensajeDiv.innerHTML = `<div class="alert ${data.success ? 'alert-success' : 'alert-warning'}">${data.mensaje}</div>`;
            if (data.success) {
                if (tipo === 'entrada') btnEntrada.disabled = true;
                if (tipo === 'salida') btnSalida.disabled = true;
            }
        })
        .catch(() => {
            mensajeDiv.innerHTML = '<div class="alert alert-danger">❌ Error al enviar asistencia.</div>';
        });
    }

    btnEntrada.addEventListener('click', () => enviarAsistencia('entrada'));
    btnSalida.addEventListener('click', () => {
        if (confirm('¿Está seguro que desea marcar su salida?')) {
            enviarAsistencia('salida');
        }
    });
});
</script>



<!-- Modal de Confirmación de Salida -->
<div class="modal fade" id="modalSalida" tabindex="-1" aria-labelledby="modalSalidaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="modalSalidaLabel">Confirmar Asistencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        Ya marcó su entrada. ¿Desea marcar su salida?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnConfirmarSalida">Marcar Salida</button>
      </div>
    </div>
  </div>
</div>
	
</body>
</html>
