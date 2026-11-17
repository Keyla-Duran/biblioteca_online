
<?php
session_start();
require_once "../config/conexion.php";

// Verificar que sea bibliotecario
if (!isset($_SESSION["id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "Bibliotecario") {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";

// Aprobar solicitud
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["aprobar"])) {

    $id = (int) $_POST["aprobar"];

    try {
        $conexion->begin_transaction();

        // Buscar solicitud pendiente y bloquearla
        $stmt = $conexion->prepare(
            "SELECT book_id FROM transactions
             WHERE id = ? AND status = 'Pendiente'
             FOR UPDATE"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $solicitud = $stmt->get_result()->fetch_assoc();

        if (!$solicitud) {
            throw new Exception("La solicitud ya fue procesada o no existe.");
        }

        $book_id = (int) $solicitud["book_id"];

        // Descontar un ejemplar solamente si hay disponibilidad
        $stmt = $conexion->prepare(
            "UPDATE books SET quantity = quantity - 1
             WHERE id = ? AND quantity > 0"
        );

        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        if ($stmt->affected_rows !== 1) {
            throw new Exception("No hay ejemplares disponibles.");
        }

        // Cambiar estado de la solicitud
        $stmt = $conexion->prepare(
            "UPDATE transactions SET status = 'Prestado'
             WHERE id = ? AND status = 'Pendiente'"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows !== 1) {
            throw new Exception("No se pudo actualizar la solicitud.");
        }

        $conexion->commit();
        $mensaje = "Préstamo aprobado correctamente.";

    } catch (Throwable $e) {
        $conexion->rollback();
        $mensaje = $e->getMessage();
    }
}

// Consultar solicitudes pendientes
$sql = "SELECT
            transactions.id,
            users.username,
            books.title,
            transactions.date_of_issue,
            transactions.status
        FROM transactions
        INNER JOIN users ON transactions.user_id = users.id
        INNER JOIN books ON transactions.book_id = books.id
        WHERE transactions.status = 'Pendiente'
        ORDER BY transactions.id DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de préstamos</title>

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

    <h2>Solicitudes de préstamos</h2>
    <p>Revisa y aprueba las solicitudes de los lectores.</p>

    <?php if ($mensaje !== ""): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mt-4">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Lector</th>
                            <th>Libro</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($resultado && $resultado->num_rows > 0): ?>

                        <?php while ($fila = $resultado->fetch_assoc()): ?>

                            <tr>
                                <td><?php echo (int)$fila["id"]; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($fila["username"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($fila["title"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($fila["date_of_issue"]); ?>
                                </td>

                                <td>
                                    <span class="badge bg-warning text-dark">
                                        Pendiente
                                    </span>
                                </td>

                                <td>
                                    <form method="POST">
                                        <button
                                            type="submit"
                                            name="aprobar"
                                            value="<?php echo (int)$fila["id"]; ?>"
                                            class="btn btn-success btn-sm">
                                            Aprobar
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                No hay solicitudes pendientes.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">
        Volver al panel
    </a>

</div>

</body>
</html>
