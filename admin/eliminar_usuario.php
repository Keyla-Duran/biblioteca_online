
<?php
session_start();
require_once "../config/conexion.php";

// Verificar que sea administrador
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "Administrador") {
    header("Location: ../login.php");
    exit();
}

// Verificar que se haya enviado un ID
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"])) {
    header("Location: usuarios.php");
    exit();
}

$id = (int) $_POST["id"];

// Evitar que el administrador se elimine a sí mismo
if ($id === (int) $_SESSION["id"]) {
    header("Location: usuarios.php?error=propio");
    exit();
}

// Eliminar usuario
$stmt = $conexion->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: usuarios.php");
exit();
