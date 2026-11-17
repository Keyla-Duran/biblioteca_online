
<?php
session_start();

require_once "../config/conexion.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["id"])) {
    header("Location: ../login.php");
    exit();
}

// Consultar todos los libros
$sql = "SELECT * FROM books ORDER BY title ASC";
$libros = $conexion->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catálogo - Biblioteca Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">
</head>

<body class="bg-light">

<!-- Barra de navegación -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container">

        <span class="navbar-brand">
             Biblioteca Online
        </span>

        <div>
            <?php if ($_SESSION["rol"] === "Lector"): ?>

                <a href="historial.php" class="btn btn-primary me-2">
                    Mis préstamos
                </a>

            <?php elseif ($_SESSION["rol"] === "Bibliotecario"): ?>

                <a href="../bibliotecario/dashboard.php" class="btn btn-secondary me-2">
                    Volver al panel
                </a>

            <?php elseif ($_SESSION["rol"] === "Administrador"): ?>

                <a href="../admin/dashboard.php" class="btn btn-secondary me-2">
                    Volver al panel
                </a>

            <?php endif; ?>

            <a href="../logout.php" class="btn btn-danger">
                Cerrar sesión
            </a>

        </div>

    </div>
</nav>

<!-- Contenido principal -->
<div class="container mt-5">

    <h2 class="mb-2">Catálogo de libros</h2>

    <p class="text-muted mb-4">
        Bienvenido,
        <?php echo htmlspecialchars($_SESSION["username"]); ?>.

        Explora los libros disponibles en nuestra biblioteca.
    </p>

    <?php if (isset($_GET["solicitud"]) && $_GET["solicitud"] === "ok"): ?>

        <div class="alert alert-success">
            ¡Tu solicitud de préstamo fue registrada correctamente!
        </div>

    <?php endif; ?>

    <div class="row g-4">

        <?php if ($libros->num_rows > 0): ?>

            <?php while ($libro = $libros->fetch_assoc()): ?>

                <div class="col-md-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($libro["title"]); ?>
                            </h5>

                            <p class="card-text">

                                <strong>Autor:</strong>
                                <?php echo htmlspecialchars($libro["author"]); ?>

                                <br>

                                <strong>Año:</strong>
                                <?php echo htmlspecialchars($libro["year"] ?? "No especificado"); ?>

                                <br>

                                <strong>Género:</strong>
                                <?php echo htmlspecialchars($libro["genre"] ?? "No especificado"); ?>

                                <br>

                                <strong>Disponibles:</strong>
                                <?php echo (int) $libro["quantity"]; ?>

                            </p>

                            <?php if ($libro["quantity"] > 0): ?>

                                <span class="badge bg-success mb-3">
                                    Disponible
                                </span>

                                <?php if ($_SESSION["rol"] === "Lector"): ?>

                                    <form action="solicitar_prestamo.php" method="POST">

                                        <input
                                            type="hidden"
                                            name="book_id"
                                            value="<?php echo (int) $libro["id"]; ?>"
                                        >

                                        <button type="submit" class="btn btn-primary w-100">
                                            Solicitar préstamo
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <p class="text-muted mt-2">
                                        Consulta de catálogo
                                    </p>

                                <?php endif; ?>

                            <?php else: ?>

                                <span class="badge bg-danger">
                                    No disponible
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12">

                <div class="alert alert-info">
                    Todavía no hay libros registrados en la biblioteca.
                </div>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
