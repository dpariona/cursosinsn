<?php
include("../config/db_con.php");

$encuesta_id = isset($_GET['encuesta_id']) ? intval($_GET['encuesta_id']) : 0;

if ($encuesta_id <= 0) {
    echo "<div class='alert alert-warning'>❗ Encuesta no válida.</div>";
    exit;
}

// Obtener preguntas
$sql_preguntas = "SELECT id, txt_pregunta FROM encuesta_preguntas WHERE id_encuesta = $encuesta_id";
$res_preguntas = $db_con->query($sql_preguntas);

if ($res_preguntas->num_rows > 0): ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<div class="container mt-4">
    <h2 class="mb-4">Resultados de la Encuesta</h2>

    <?php while ($pregunta = $res_preguntas->fetch_assoc()):
        $id_pregunta = $pregunta['id'];
        $txt_pregunta = $pregunta['txt_pregunta'];

        // Obtener opciones y respuestas
        $sql_opciones = "
            SELECT o.id, o.txt_opcion, COUNT(r.id) AS total
            FROM encuesta_opciones_respuesta o
            LEFT JOIN encuesta_respuestas r ON r.id_opcion = o.id
            WHERE o.id_pregunta = $id_pregunta
            GROUP BY o.id, o.txt_opcion
        ";
        $res_opciones = $db_con->query($sql_opciones);

        $labels = [];
        $datos = [];

        while ($fila = $res_opciones->fetch_assoc()) {
            $labels[] = $fila['txt_opcion'];
            $datos[] = intval($fila['total']);
        }

        $json_labels = json_encode($labels);
        $json_datos = json_encode($datos);
        $canvas_barra = "grafico_barra_$id_pregunta";
        $canvas_torta = "grafico_torta_$id_pregunta";
    ?>

    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong><?= htmlspecialchars($txt_pregunta) ?></strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h6>Gráfico de Barras</h6>
                    <canvas id="<?= $canvas_barra ?>"></canvas>
                </div>
                <div class="col-md-6 mb-4">
                    <h6>Gráfico de Torta</h6>
                    <canvas id="<?= $canvas_torta ?>"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const labels_<?= $id_pregunta ?> = <?= $json_labels ?>;
        const datos_<?= $id_pregunta ?> = <?= $json_datos ?>;

        new Chart(document.getElementById("<?= $canvas_barra ?>"), {
            type: 'bar',
            data: {
                labels: labels_<?= $id_pregunta ?>,
                datasets: [{
                    label: 'Votos',
                    data: datos_<?= $id_pregunta ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, stepSize: 1 } }
            }
        });

        new Chart(document.getElementById("<?= $canvas_torta ?>"), {
            type: 'pie',
            data: {
                labels: labels_<?= $id_pregunta ?>,
                datasets: [{
                    data: datos_<?= $id_pregunta ?>,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1']
                }]
            },
            options: { responsive: true }
        });
    </script>

    <?php endwhile; ?>
</div>

<?php else: ?>
    <div class="alert alert-info">No hay preguntas disponibles en esta encuesta.</div>
<?php endif; ?>
