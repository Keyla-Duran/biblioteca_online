
<?php
session_start();

require_once "../config/conexion.php";

// Verificar que sea administrador
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "Administrador") {
    header("Location: ../login.php");
    exit();
}

// Consultar todas las transacciones
$sql = "SELECT
            transactions.id,
            users.username,
            books.title,
            books.author,
            transactions.date_of_issue,
            transactions.date_of_return,
            transactions.status
        FROM transactions
        INNER JOIN users ON transactions.user_id = users.id
        INNER JOIN books ON transactions.book_id = books.id
        ORDER BY transactions.id DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transacciones - Biblioteca Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Biblioteca Online - Administrador</span>

        <a href="../logout.php" class="btn btn-danger">
            Cerrar sesión
        </a>
    </div>
</nav>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Historial de transacciones</h2>

        <a href="dashboard.php" class="btn btn-secondary">
            Volver al panel
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Todos los préstamos y devoluciones</h5>

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Lector</th>
                            <th>Libro</th>
                            <th>Autor</th>
                            <th>Fecha de préstamo</th>
                            <th>Fecha de devolución</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($resultado->num_rows > 0): ?>

                        <?php while ($fila = $resultado->fetch_assoc()): ?>

                            <tr>
                                <td><?php echo (int) $fila["id"]; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($fila["username"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($fila["title"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($fila["author"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($fila["date_of_issue"]); ?>
                                </td>

                                <td>
                                    <?php
                                    echo $fila["date_of_return"]
                                        ? htmlspecialchars($fila["date_of_return"])
                                        : "Sin devolución";
                                    ?>
                                </td>

                                <td>
                                    <?php if ($fila["status"] === "Pendiente"): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>

                                    <?php elseif ($fila["status"] === "Prestado"): ?>
                                        <span class="badge bg-primary">Prestado</span>

                                    <?php else: ?>
                                        <span class="badge bg-success">Devuelto</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                Todavía no hay transacciones registradas.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>
