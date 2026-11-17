
<?php
session_start();

require_once "../config/conexion.php";

// Verificar que el usuario sea lector
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "Lector") {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION["id"];

// Consultar los préstamos del lector que inició sesión
$sql = "SELECT
            transactions.id,
            books.title,
            books.author,
            transactions.date_of_issue,
            transactions.date_of_return,
            transactions.status
        FROM transactions
        INNER JOIN books ON transactions.book_id = books.id
        WHERE transactions.user_id = ?
        ORDER BY transactions.id DESC";

$consulta = $conexion->prepare($sql);
$consulta->bind_param("i", $id_usuario);
$consulta->execute();

$prestamos = $consulta->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis préstamos - Biblioteca Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Mis préstamos</h2>

        <a href="catalogo.php" class="btn btn-secondary">
            Volver al catálogo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Historial de solicitudes y préstamos</h5>

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Libro</th>
                            <th>Autor</th>
                            <th>Fecha de solicitud</th>
                            <th>Fecha de devolución</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($prestamos->num_rows > 0): ?>

                        <?php while ($fila = $prestamos->fetch_assoc()): ?>

                            <tr>
                                <td><?php echo $fila["id"]; ?></td>

                                <td><?php echo htmlspecialchars($fila["title"]); ?></td>

                                <td><?php echo htmlspecialchars($fila["author"]); ?></td>

                                <td><?php echo htmlspecialchars($fila["date_of_issue"]); ?></td>

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
                            <td colspan="6" class="text-center">
                                Todavía no tienes solicitudes de préstamo.
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
