
<?php
session_start();
require_once "../config/conexion.php";

// PROTEGER EL ACCESO
if (!isset($_SESSION["id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "Administrador") {

    header("Location: ../login.php");
    exit();
}

// OBTENER EL ID DEL USUARIO
$id = (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: usuarios.php");
    exit();
}

// BUSCAR USUARIO
$stmt = $conexion->prepare(
    "SELECT id, username, email, role_id FROM users WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: usuarios.php");
    exit();
}

$usuario = $resultado->fetch_assoc();

$mensaje = "";
$tipoMensaje = "danger";

// ACTUALIZAR USUARIO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $rol_id = (int) ($_POST["role_id"] ?? 0);

    if ($nombre === "" || $email === "" || $rol_id <= 0) {

        $mensaje = "Completa todos los campos obligatorios.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo electrónico no es válido.";

    } elseif ($password !== "" && strlen($password) < 8) {

        $mensaje = "La contraseña debe tener al menos 8 caracteres.";

    } else {

        try {

            // CAMBIAR CONTRASEÑA SOLO SI SE ESCRIBE UNA NUEVA
            if ($password !== "") {

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "UPDATE users
                        SET username = ?, email = ?,
                            password = ?, role_id = ?
                        WHERE id = ?";

                $actualizar = $conexion->prepare($sql);

                $actualizar->bind_param(
                    "sssii",
                    $nombre,
                    $email,
                    $hash,
                    $rol_id,
                    $id
                );

            } else {

                $sql = "UPDATE users
                        SET username = ?, email = ?, role_id = ?
                        WHERE id = ?";

                $actualizar = $conexion->prepare($sql);

                $actualizar->bind_param(
                    "ssii",
                    $nombre,
                    $email,
                    $rol_id,
                    $id
                );
            }

            $actualizar->execute();

            header("Location: usuarios.php");
            exit();

        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                $mensaje = "Ese correo ya pertenece a otro usuario.";
            } else {
                $mensaje = "No se pudieron guardar los cambios.";
            }
        }
    }

    // ACTUALIZAR LOS DATOS MOSTRADOS
    $usuario["username"] = $nombre;
    $usuario["email"] = $email;
    $usuario["role_id"] = $rol_id;
}

// CONSULTAR ROLES
$roles = $conexion->query("SELECT * FROM roles");

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Editar usuario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="../css/estilos.css?v=2">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary p-3">

    <div class="container">

        <span class="navbar-brand">
            📚 Biblioteca Online
        </span>

        <a href="usuarios.php" class="btn btn-light btn-sm">
            Volver
        </a>

    </div>

</nav>


<div class="container mt-5">

    <div class="card shadow-sm border-0 mx-auto"
         style="max-width: 650px;">

        <div class="card-header bg-white">

            <h4 class="mb-0">
                Editar usuario
            </h4>

        </div>


        <div class="card-body">

            <?php if ($mensaje !== ""): ?>

                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>

            <?php endif; ?>


            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        Nombre de usuario
                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        maxlength="50"
                        value="<?php echo htmlspecialchars($usuario["username"]); ?>"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($usuario["email"]); ?>"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Nueva contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        minlength="8"
                    >

                    <small class="text-muted">
                        Déjala vacía si no deseas cambiarla.
                    </small>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Rol del usuario
                    </label>

                    <select name="role_id"
                            class="form-select"
                            required>

                        <?php while ($rol = $roles->fetch_assoc()): ?>

                            <option
                                value="<?php echo $rol["id"]; ?>"
                                <?php echo ($rol["id"] == $usuario["role_id"]) ? "selected" : ""; ?>
                            >
                                <?php echo htmlspecialchars($rol["name"]); ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <button type="submit" class="btn btn-primary">
                    Guardar cambios
                </button>

                <a href="usuarios.php" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>
