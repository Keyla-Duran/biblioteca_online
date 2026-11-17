
<?php
session_start();

require_once "../config/conexion.php";

// Verificar que el usuario sea bibliotecario
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "Bibliotecario") {
    header("Location: ../login.php");
    exit();
}

// Consultar estadísticas
$total_libros = $conexion->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()["total"];

$total_prestamos = $conexion->query("SELECT COUNT(*) AS total FROM transactions")->fetch_assoc()["total"];

$pendientes = $conexion->query("SELECT COUNT(*) AS total FROM transactions WHERE status = 'Pendiente'")->fetch_assoc()["total"];

$prestados = $conexion->query("SELECT COUNT(*) AS total FROM transactions WHERE status = 'Prestado'")->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel del Bibliotecario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Biblioteca Online - Bibliotecario</span>

        <a href="../logout.php" class="btn btn-danger">
            Cerrar sesión
        </a>
    </div>
</nav>

<div class="container mt-5">

    <h2 class="mb-2">
        ¡Bienvenido, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
    </h2>

    <p class="text-muted">Panel de administración del bibliotecario.</p>

    <hr>

    <h4 class="mb-3">Resumen de la biblioteca</h4>

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary"><?php echo $total_libros; ?></h2>
                    <p class="mb-0">Libros registrados</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h2 class="text-dark"><?php echo $total_prestamos; ?></h2>
                    <p class="mb-0">Solicitudes y préstamos</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h2 class="text-warning"><?php echo $pendientes; ?></h2>
                    <p class="mb-0">Solicitudes pendientes</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h2 class="text-success"><?php echo $prestados; ?></h2>
                    <p class="mb-0">Libros prestados</p>
                </div>
            </div>
        </div>

    </div>

    <h4 class="mb-3">Gestión de la biblioteca</h4>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Gestión de libros</h5>
                    <p>Agregar y consultar los libros disponibles.</p>
                    <a href="libros.php" class="btn btn-primary">
                        Gestionar libros
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Solicitudes de préstamo</h5>
                    <p>Revisar y aprobar las solicitudes de los lectores.</p>
                    <a href="prestamos.php" class="btn btn-warning">
                        Gestionar préstamos
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Devoluciones</h5>
                    <p>Registrar la devolución de los libros prestados.</p>
                    <a href="devoluciones.php" class="btn btn-success">
                        Registrar devoluciones
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>
