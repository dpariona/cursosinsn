<?php
//nuevo_curso_inscribirse.php
include 'config/db_con.php';
require 'estudiantes/PHPMailer/src/PHPMailer.php';
require 'estudiantes/PHPMailer/src/SMTP.php';
require 'estudiantes/PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$curso_id = $_GET['curso_id'] ?? null;
$ci = $_POST['ci'] ?? null;
$confirmar = $_POST['confirmar'] ?? null;

// Buscar curso
$curso = $db_con->query("SELECT * FROM cursos WHERE id = '$curso_id'")->fetch_assoc();
if (!$curso) die("Curso no encontrado.");

$mensaje = '';
$est = null;

if ($confirmar && $ci) {
    $est = $db_con->query("SELECT * FROM dat_admin WHERE ci = '$ci'")->fetch_assoc();
    if ($est) {
        //$examen = $db_con->query("SELECT * FROM examen WHERE id_curso = '$curso_id' AND estado = 'Publicado'")->fetch_assoc();
		$examen = $db_con->query("SELECT * FROM examen WHERE id_curso = '$curso_id' ")->fetch_assoc();
        if ($examen) {
            $id_examen = $examen['id'];
            $ya = $db_con->query("SELECT * FROM cuestionarios WHERE ci = '$ci' AND id_examen = '$id_examen'");
            if ($ya->num_rows == 0) {
                // Insertar en cuestionarios
                $db_con->query("INSERT INTO cuestionarios (ci, ap, am, nom, cargo, id_datos, id_examen)
                                VALUES ('{$est['ci']}', '{$est['ap']}', '{$est['am']}', '{$est['nom']}', '{$est['cargo']}', '{$est['id']}', '$id_examen')");

                // Insertar en inscripciones
                $cod_renipress = $est['cod_renipress'] ?? '';
                $db_con->query("INSERT INTO inscripciones (ci, ap, am, nom, cargo, id_datos, id_examen, id_curso, voucher, archivo, fecha_reg, cod_renipress)
                                VALUES ('{$est['ci']}', '{$est['ap']}', '{$est['am']}', '{$est['nom']}', '{$est['cargo']}', '{$est['id']}', '$id_examen', '$curso_id', '', '', CURRENT_TIMESTAMP, '$cod_renipress')");

                // Obtener establecimiento
                $establecimiento = 'No especificado';
                $renipress = $db_con->prepare("SELECT Establecimiento, departamento, Provincia, Distrito FROM renipressss WHERE codigo_unico = ?");
                $renipress->bind_param("s", $cod_renipress);
                $renipress->execute();
                $renipress->bind_result($e, $d, $p, $di);
                if ($renipress->fetch()) {
                    $establecimiento = "$e - $d - $p - $di";
                }
                $renipress->close();

                // Enviar correo
                $correo = $est['correo'] ?? '';
                $nom = $est['nom'];
                $gocu = $est['gocu'] ?? 'No especificado';
                $colabo = $est['colabo'] ?? 'No especificado';
                $tiplab = $est['tiplab'] ?? 'No especificado';

                if (!empty($correo)) {
                    $mail = new PHPMailer(true);
                    $mail->CharSet = 'UTF-8';
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'mail.dnt.net.pe';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'info@dnt.net.pe';
                        $mail->Password = 'Passw0rd.huk';
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = 465;

                        $mail->setFrom('info@dnt.net.pe', 'INSN');
                        $mail->addAddress($correo, $nom);
                        $mail->isHTML(true);
                        $mail->Subject = 'Confirmación de Inscripción al Curso INSN';
                        $mail->Body = "
                            <h3>Hola $nom,</h3>
                            <p>Tu inscripción ha sido registrada exitosamente.</p>
                            <h3>Curso: <b>{$curso['titulo_curso']}</b></h3>
							<p style='color: #751aff; text-decoration: none;'><strong>Usuario (CI/DNI):</strong> $ci</p>
                            <p><strong>Grupo Ocupacional:</strong> $gocu<br>
                            <strong>Procedencia:</strong> $establecimiento</p>
                            <p><strong>Condición Laboral:</strong> $colabo<br>
                            <strong>Tipo de Labor:</strong> $tiplab</p>
                            <hr>

							<p>Gracias por inscribirte.</p>
							
							";
                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Error al enviar correo: " . $mail->ErrorInfo);
                    }
                }

                $mensaje = "✅ Inscripción completada exitosamente.";
            } else {
                $mensaje = "⚠ Ya estás inscrito en este curso.";
            }
        } else {
            $mensaje = "⚠ Este curso aún no tiene examen publicado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inscripción a Cursos INSN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .img-fluid {
            max-height: 700px;
            object-fit: cover;
            cursor: pointer;
        }
        .modal-body img {
            max-width: 100%;
            height: auto;
        }
        @media (max-width: 767.98px) {
            .row.no-gutters {
                flex-direction: column;
            }
            .col-md-5, .col-md-7 {
                max-width: 100%;
                flex: 0 0 100%;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="card shadow-lg">
        <div class="row no-gutters">
            <div class="col-md-5">
                <img src="imagenes/<?= htmlspecialchars($curso['imagen']) ?>" class="img-fluid w-100" alt="Imagen del curso" data-toggle="modal" data-target="#modalImagen">
            </div>
			<div class="col-md-7">
				<div class="card-body">
					<h4 class="card-title text-primary"><?= htmlspecialchars($curso['titulo_curso']) ?></h4>
					<hr>
					<?php if (!empty($mensaje)): ?>
						<div class="alert alert-info"><?= $mensaje ?></div>

						<?php if (strpos($mensaje, 'Inscripción completada') !== false): ?>
						  <!--  <a href="encuesta.php?curso_id=<?= $curso_id ?>" class="btn btn-primary mt-3"> 
								📝 Responder encuesta del curso
							</a>-->
						<?php endif; ?>

						<a href="indexlogin.php" class="btn btn-secondary">← Ingresar al Aula V</a>

					<?php elseif ($ci && !$confirmar): 
						$est = $db_con->query("SELECT * FROM dat_admin WHERE ci = '$ci'")->fetch_assoc();
						if ($est): ?>
							<div class="alert alert-success">✅ Usuario encontrado</div>
							<p><strong>DNI/CI:</strong> <?= $est['ci'] ?></p>
							<p><strong>Correo:</strong> <?= $est['correo'] ?></p>
							<p><strong>Nombre:</strong> <?= $est['ap'] . ' ' . $est['am'] . ', ' . $est['nom'] ?></p>
							<p><strong>Profesión:</strong> <?= $est['gocu'] ?></p>
							<form method="post">
								<input type="hidden" name="ci" value="<?= $ci ?>">
								<input type="hidden" name="confirmar" value="1">
								<button type="submit" class="btn btn-success">✅ Confirmar Inscripción</button>
								<a href="/" class="btn btn-secondary">❌ Cancelar</a>
							</form>
							<?php else: ?>
							<div class="alert alert-warning" role="alert">
								⚠ DNI/CI <strong><?= htmlspecialchars($ci) ?></strong> Usuario no registrado.<br>
								➤ Por favor, haz clic en el botón <strong>"Nuevo Usuario"</strong> para completar tu registro.  
								<span class="text-danger">(Se realiza una sola vez y debe registrar datos correctos para su <strong>CERTIFICADO</strong>).</span><br>
								➤ Para futuros cursos ya estarás registrado en nuestra base de datos.<br>
								➤ Una vez finalizado el registro, regresa a esta página para <strong>confirmar tu inscripción al curso</strong>.
							</div>
								<a href="<?= htmlspecialchars($curso['formulario']) ?>?ci=<?= urlencode($ci) ?>&curso_id=<?= urlencode($curso_id) ?>" class="btn btn-outline-danger">➕ Nuevo Usuario</a>
								<a href="javascript:history.back()" class="btn btn-secondary">❌ Cancelar</a>
							<?php endif; 

									else: ?>
										<form method="post" class="mt-3">
											<div class="form-group">
												<label for="tipo_doc">Tipo de Documento:</label>
												<select name="tipo_doc" id="tipo_doc" class="form-control" required>
													<option value="">Seleccione...</option>
													<option value="DNI">DNI (8 dígitos)</option>
													<option value="C.E">C.E / C.I. (hasta 18 caracteres)</option>
												</select>
											</div>
											<div class="form-group">
												<label for="ci">Número de Documento:</label>
												<input type="text" name="ci" id="ci" class="form-control" autocomplete="off" required>
												<small id="ciHelp" class="form-text text-muted">Ingrese su número de documento.</small>
											</div>
											<div class="d-flex align-items-center">
												<button type="submit" class="btn btn-primary mr-2">🔍 Buscar</button>
												<button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAyuda">❓ Ayuda</button>
											</div>

										</form>
									<?php endif; ?>
				</div>
			</div>

		</div>
	</div>
</div>

<!-- Modal para imagen -->
<div class="modal fade" id="modalImagen" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg">
	<div class="modal-content bg-dark">
	  <div class="modal-body text-center p-0">
		<img src="imagenes/<?= htmlspecialchars($curso['imagen']) ?>" alt="Imagen ampliada del curso">
	  </div>
	</div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

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
            ciHelp.textContent = 'Hasta 18 caracteres (Ej: 123456789012-BR).';
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


<!-- Modal Ayuda -->
<div class="modal fade" id="modalAyuda" tabindex="-1" role="dialog" aria-labelledby="modalAyudaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalAyudaLabel">Ayuda</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Inscripción al Curso Virtual – INSN</strong></p>

        <p><strong>Paso 1: Buscar en la base de datos</strong><br>
        Ingresa tu número de documento según el tipo seleccionado:
        <ul>
          <li><strong>DNI:</strong> Debe tener exactamente 8 dígitos numéricos.</li>
          <li><strong>C.E / C.I:</strong> Hasta 18 caracteres alfanuméricos (Ej: 12345678-BR).</li>
        </ul>
        Si ya estás registrado, verás tus datos. Solo debes hacer clic en el botón <strong>"Confirmar Inscripción"</strong>.</p>

        <p><strong>Paso 2: Registro de nuevo usuario</strong><br>
        Si no estás registrado, el sistema mostrará un botón <strong>"Nuevo Usuario"</strong>. Haz clic allí para registrarte en el Aula Virtual.  
        Al completar el registro, recibirás un correo con el asunto: <em>"Registro exitoso - Aula Virtual INSN"</em>.</p>

        <p><strong>Paso 3: Confirmar inscripción al curso</strong><br>
        Después de registrarte como nuevo usuario, volverá al Paso 1 e ingresa nuevamente tu número de documento.  
        Ahora podrás confirmar tu inscripción al curso haciendo clic en el botón "Confirmar Inscripción".  
        Recibirás un correo con el asunto: <em>"Confirmación de Inscripción al Curso INSN"</em>.</p>

        <hr>
        <p class="mb-0"><strong>Nota:</strong> Verifica también tu bandeja de spam o correo no deseado si no ves los mensajes en tu bandeja principal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>

