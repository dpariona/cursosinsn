<?php
// login/timeout.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Sesión Expirada</title>

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">


</head>
<body>
<main id="main" class="main">
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
		<div class="card mb-3">
            <div class="card-body">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-alarm-fill"></i> Sesión expirada</h4>

                </div>
                <div class="card-body text-center">				 
					<p>Tu sesión ha expirado por inactividad.</p>
					<a href="login.php">Iniciar sesión nuevamente</a>                   
                </div>
            </div>
        </div>
    </section>
</main>

</body>
</html>




