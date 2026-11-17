
<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION["id"]) ||
    $_SESSION["rol"] !== "Bibliotecario") {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";

// Registrar libro
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["agregar"])) {

    $titulo = trim($_POST["titulo"]);
    $autor = trim($_POST["autor"]);
    $anio = (int) $_POST["anio"];
    $genero = trim($_POST["genero"]);
    $cantidad = (int) $_POST["cantidad"];

    if ($titulo !== "" && $autor !== "" && $cantidad >= 0) {

        $stmt = $conexion->prepare(
            "INSERT INTO books (title, author, year, genre, quantity)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssisi",
            $titulo,
            $autor,
            $anio,
            $genero,
            $cantidad
        );

        if ($stmt->execute()) {
            $mensaje = "Libro agregado correctamente.";
        } else {
            $mensaje = "Error al agregar el libro.";
        }

    } else {
        $mensaje = "Completa los campos obligatorios.";
    }
}

// Consultar libros
$resultado = $conexion->query(
    "SELECT * FROM books ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Biblioteca Online</a>
        <a href="../logout.php" class="btn btn-light">Cerrar sesión</a>
    </div>
</nav>

<div class="container mt-5">

    <h2>Gestión de libros</h2>

    <?php if ($mensaje !== ""): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mt-4">
        <div class="card-body">

            <h4>Agregar nuevo libro</h4>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Autor</label>
                    <input type="text" name="autor" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Año</label>
                    <input type="number" name="anio" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Género</label>
                    <input type="text" name="genero" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" min="0" required>
                </div>

                <button type="submit" name="agregar" class="btn btn-primary">
                    Agregar libro
                </button>

            </form>

        </div>
    </div>

    <h4 class="mt-5">Libros registrados</h4>

    <div class="table-responsive">
        <table class="table table-striped table-bordered bg-white">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Año</th>
                    <th>Género</th>
                    <th>Cantidad</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($libro = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo (int)$libro["id"]; ?></td>
                        <td><?php echo htmlspecialchars($libro["title"]); ?></td>
                        <td><?php echo htmlspecialchars($libro["author"]); ?></td>
                        <td><?php echo htmlspecialchars($libro["year"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($libro["genre"] ?? ""); ?></td>
                        <td><?php echo (int)$libro["quantity"]; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al panel</a>

</div>

</body>
</html>
