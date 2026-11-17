
<?php
session_start();

require_once "../config/conexion.php";

// Verificar que haya iniciado sesión un bibliotecario
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "Bibliotecario") {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";

// Registrar devolución
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["devolver"])) {

    $id_prestamo = (int) $_POST["devolver"];

    $conexion->begin_transaction();

    try {
        // Buscar el préstamo y bloquearlo mientras se procesa
        $consulta = $conexion->prepare(
            "SELECT book_id FROM transactions
             WHERE id = ? AND status = 'Prestado'
             FOR UPDATE"
        );

        $consulta->bind_param("i", $id_prestamo);
        $consulta->execute();

        $resultado = $consulta->get_result();

        if ($resultado->num_rows === 0) {
            throw new Exception("El préstamo no existe o ya fue devuelto.");
        }

        $prestamo = $resultado->fetch_assoc();
        $id_libro = (int) $prestamo["book_id"];

        // Marcar el préstamo como devuelto
        $actualizar = $conexion->prepare(
            "UPDATE transactions
             SET status = 'Devuelto', date_of_return = CURDATE()
             WHERE id = ? AND status = 'Prestado'"
        );

        $actualizar->bind_param("i", $id_prestamo);
        $actualizar->execute();

        if ($actualizar->affected_rows !== 1) {
            throw new Exception("No se pudo registrar la devolución.");
        }

        // Devolver una unidad al inventario
        $inventario = $conexion->prepare(
            "UPDATE books SET quantity = quantity + 1 WHERE id = ?"
        );

        $inventario->bind_param("i", $id_libro);
        $inventario->execute();

        if ($inventario->affected_rows !== 1) {
            throw new Exception("No se pudo actualizar el inventario.");
        }

        $conexion->commit();

        $mensaje = "¡Devolución registrada correctamente!";

    } catch (Exception $error) {
        $conexion->rollback();
        $mensaje = $error->getMessage();
    }
}

// Consultar libros que están prestados
$sql = "SELECT
            transactions.id,
            users.username,
            books.title,
            books.author,
            transactions.date_of_issue
        FROM transactions
        INNER JOIN users ON transactions.user_id = users.id
        INNER JOIN books ON transactions.book_id = books.id
        WHERE transactions.status = 'Prestado'
        ORDER BY transactions.date_of_issue ASC";

$prestamos = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Devoluciones - Biblioteca Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Registrar devoluciones</h2>
        <a href="dashboard.php" class="btn btn-secondary">Volver al panel</a>
    </div>

    <?php if ($mensaje !== ""): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Libros actualmente prestados</h5>

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Lector</th>
                            <th>Libro</th>
                            <th>Autor</th>
                            <th>Fecha de préstamo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($prestamos->num_rows > 0): ?>

                        <?php while ($fila = $prestamos->fetch_assoc()): ?>

                            <tr>
                                <td><?php echo $fila["id"]; ?></td>

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
                                    <form method="POST" onsubmit="return confirm('¿Confirmas que el libro fue devuelto?');">

                                        <button
                                            type="submit"
                                            name="devolver"
                                            value="<?php echo $fila["id"]; ?>"
                                            class="btn btn-success btn-sm">
                                            Registrar devolución
                                        </button>

                                    </form>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                No hay libros pendientes de devolución.
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
