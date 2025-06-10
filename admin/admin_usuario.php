<?php
require_once '../templates/header_admin.php'; // Ya incluye config y control de sesión
require_once '../templates/sidebar_admin.php'; 


// Restringir solo a superadmin
if (Sesion::get('rol') !== 'superadmin') {
    echo "Acceso denegado. Solo para superadmin.";
    exit;
}

// Consultar usuarios
$sql = "SELECT id, ci, username, nombre, correo, rol, estado, foto FROM usuarios ORDER BY nombre ASC";
$result = $db_con->query($sql);
?>


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="row">
            <div class="card shadow-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">📋 Encuestas</h4>
                    <a href="admin_encuesta_nueva.php" class="btn btn-info text-white">➕ Nuevo Usuario</a>
                </div>
                <div class="card-body">	
				  <table class="table datatable table-bordered table-hover">
					<thead class="thead-dark">
					  <tr>
						<th>Foto</th>
						<th>CI</th>
						<th>Username</th>
						<th>Nombre</th>
						<th>Correo</th>
						<th>Rol</th>
						<th>Estado</th>
						<th>Acciones</th>
					  </tr>
					</thead>
					<tbody>
					  <?php if ($result && $result->num_rows > 0): ?>
						<?php while ($row = $result->fetch_assoc()): ?>
						  <tr>
							<td>
							  <?php if ($row['foto'] && file_exists("../assets/uploads/usuarios/" . $row['foto'])): ?>
								<img src="../assets/uploads/usuarios/<?= htmlspecialchars($row['foto']) ?>" class="foto">
							  <?php else: ?>
								<img src="../assets/uploads/usuarios/user.png" class="foto">
							  <?php endif; ?>
							</td>
							<td><?= htmlspecialchars($row['ci']) ?></td>
							<td><?= htmlspecialchars($row['username']) ?></td>
							<td><?= htmlspecialchars($row['nombre']) ?></td>
							<td><?= htmlspecialchars($row['correo']) ?></td>
							<td><?= htmlspecialchars($row['rol']) ?></td>
							<td>
							  <span class="badge bg-<?= $row['estado'] == 'activo' ? 'success' : 'secondary' ?>">
								<?= ucfirst($row['estado']) ?>
							  </span>
							</td>
							<td>
							  <a href="usuario_editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
							  <!-- Aquí puedes agregar botón para cambiar estado o eliminar -->
							</td>
						  </tr>
						<?php endwhile; ?>
					  <?php else: ?>
						<tr>
						  <td colspan="8" class="text-center">No hay usuarios registrados.</td>
						</tr>
					  <?php endif; ?>
					</tbody>
				  </table>
				</div>
			</div>
		</div>
	</section>
</main>
<?php include '../templates/footer_admin.php'; ?>