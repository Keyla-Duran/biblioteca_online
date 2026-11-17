
<?php
session_start();
require_once "../config/conexion.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "Lector") {
    header("Location: ../login.php");
    exit();
}

// Verificar que se haya enviado el ID del libro
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["book_id"])) {
    header("Location: catalogo.php");
    exit();
}

$book_id = (int) $_POST["book_id"];
$user_id = (int) $_SESSION["id"];

// Comprobar que el libro exista y tenga ejemplares disponibles
$stmt = $conexion->prepare(
    "SELECT quantity FROM books WHERE id = ?"
);
$stmt->bind_param("i", $book_id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: catalogo.php?error=libro");
    exit();
}

$libro = $resultado->fetch_assoc();

if ((int)$libro["quantity"] <= 0) {
    header("Location: catalogo.php?error=agotado");
    exit();
}

// Registrar la solicitud
$fecha = date("Y-m-d");

$stmt = $conexion->prepare(
    "INSERT INTO transactions (user_id, book_id, date_of_issue, status)
     VALUES (?, ?, ?, 'Pendiente')"
);

$stmt->bind_param("iis", $user_id, $book_id, $fecha);

if ($stmt->execute()) {
    header("Location: catalogo.php?solicitud=ok");
    exit();
} else {
    echo "Error al registrar la solicitud.";
}
?>
