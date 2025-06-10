<?php
// admin/admin_usuario_nuevo.php
session_start();
require_once '../templates/header_admin.php'; // Ya incluye config y control de sesión
require_once '../templates/sidebar_admin.php'; 

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
	
  <div class="container mt-5">
    <h3>Registro de Nuevo Usuario</h3>
    <form action="admin_usuario_save.php" method="POST" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label for="ci" class="form-label">CI/DNI</label>
        <input type="text" name="ci" id="ci" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label for="username" class="form-label">Nombre de usuario</label>
        <input type="text" name="username" id="username" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" name="correo" id="correo" class="form-control">
      </div>
      <div class="col-md-6">
        <label for="clave" class="form-label">Contraseña</label>
        <input type="password" name="clave" id="clave" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label for="rol" class="form-label">Rol</label>
        <select name="rol" id="rol" class="form-select" required>
          <option value="admin">Administrador</option>
          <option value="superadmin">Super Administrador</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select">
          <option value="activo" selected>Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="foto" class="form-label">Foto de perfil</label>
        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">Registrar Usuario</button>
      </div>
    </form>
  </div>

</main>