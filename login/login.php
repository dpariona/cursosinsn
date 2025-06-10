<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Acceso al Sistema</title>
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <h4 class="text-center mb-3">Iniciar sesión</h4>
        <form action="login_verificar.php" method="POST">
          <div class="form-group mb-3">
            <label for="ci">DNI/CI</label>
            <input type="text" name="ci" class="form-control" required>
          </div>
          <div class="form-group mb-3">
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
