
<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION["id"]) ||
    $_SESSION["rol"] !== "Administrador") {
    header("Location: ../login.php");
    exit();
}

// Consultar cantidad de usuarios
$usuarios = $conexion->query(
    "SELECT COUNT(*) AS total FROM users"
)->fetch_assoc()["total"];

// Consultar cantidad de libros
$libros = $conexion->query(
    "SELECT COUNT(*) AS total FROM books"
)->fetch_assoc()["total"];

// Consultar préstamos
$prestamos = $conexion->query(
    "SELECT COUNT(*) AS total FROM transactions"
)->fetch_assoc()["total"];

// Consultar préstamos pendientes
$pendientes = $conexion->query(
    "SELECT COUNT(*) AS total FROM transactions
     WHERE status = 'Pendiente'"
)->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel Administrador</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow">
    <div class="container-fluid px-4">
        <span class="navbar-brand fw-bold">
             Biblioteca Online
        </span>

        <div class="d-flex align-items-center gap-3">
            <span class="text-white">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
            </span>

            <a href="../logout.php" class="btn btn-light btn-sm">
                Cerrar sesión
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid p-4">

    <h2 class="fw-bold">Panel de Administración</h2>

    <p class="text-muted">
        Bienvenido al sistema de gestión de biblioteca.
    </p>

    <!-- ESTADÍSTICAS -->

    <div class="row g-4 mt-2">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="text-muted">Usuarios registrados</h6>
                <h2 class="text-primary">
                    <?php echo $usuarios; ?>
                </h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="text-muted">Libros registrados</h6>
                <h2 class="text-success">
                    <?php echo $libros; ?>
                </h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="text-muted">Préstamos totales</h6>
                <h2 class="text-warning">
                    <?php echo $prestamos; ?>
                </h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="text-muted">Solicitudes pendientes</h6>
                <h2 class="text-danger">
                    <?php echo $pendientes; ?>
                </h2>
            </div>
        </div>

    </div>

    <!-- OPCIONES DEL SISTEMA -->

    <h4 class="mt-5 mb-3">Administración del sistema</h4>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4">
                <h5>👥 Gestión de usuarios</h5>
                <p>Registrar, consultar y administrar usuarios.</p>
                <a href="usuarios.php" class="btn btn-primary">
                    Administrar usuarios
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4">
                <h5>📖 Catálogo de libros</h5>
                <p>Consultar los libros de la biblioteca.</p>
                <a href="../lector/catalogo.php" class="btn btn-success">
                    Ver catálogo
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4">
                <h5>📋 Transacciones</h5>
                <p>Consultar los préstamos y devoluciones.</p>
                <a href="transacciones.php" class="btn btn-warning">
                    Ver transacciones
                </a>
            </div>
        </div>

    </div>

</div>

</body>
</html>
