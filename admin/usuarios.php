
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

$mensaje = "";
$tipoMensaje = "info";

// REGISTRAR USUARIO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $rol_id = (int) ($_POST["role_id"] ?? 0);

    if ($nombre === "" || $email === "" ||
        $password === "" || $rol_id <= 0) {

        $mensaje = "Todos los campos son obligatorios.";
        $tipoMensaje = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo electrónico no es válido.";
        $tipoMensaje = "danger";

    } elseif (strlen($password) < 8) {

        $mensaje = "La contraseña debe tener al menos 8 caracteres.";
        $tipoMensaje = "danger";

    } else {

        try {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users
                    (username, email, password, role_id)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conexion->prepare($sql);

            $stmt->bind_param(
                "sssi",
                $nombre,
                $email,
                $hash,
                $rol_id
            );

            $stmt->execute();

            $mensaje = "Usuario registrado correctamente.";
            $tipoMensaje = "success";

        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                $mensaje = "Este correo ya está registrado.";
            } else {
                $mensaje = "Ocurrió un error al registrar el usuario.";
            }

            $tipoMensaje = "danger";
        }
    }
}

// CONSULTAR ROLES
$roles = $conexion->query("SELECT * FROM roles");

// CONSULTAR USUARIOS
$sqlUsuarios = "SELECT users.id, users.username,
                       users.email, roles.name AS rol
                FROM users
                INNER JOIN roles
                ON users.role_id = roles.id
                ORDER BY users.id DESC";

$usuarios = $conexion->query($sqlUsuarios);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Gestión de usuarios | Biblioteca Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    
    <link rel="stylesheet" href="../css/estilos.css?v=2">

</head>

<body class="bg-light">

<!-- BARRA DE NAVEGACIÓN -->

<nav class="navbar navbar-dark bg-primary p-3">

    <div class="container">

        <span class="navbar-brand">
            📚 Biblioteca Online
        </span>

        <a href="dashboard.php" class="btn btn-light btn-sm">
            Volver al panel
        </a>

    </div>

</nav>


<div class="container mt-4">

    <h2 class="fw-bold">Gestión de usuarios</h2>

    <p class="text-muted">
        Registra y administra los usuarios de la biblioteca.
    </p>


    <!-- MENSAJES -->

    <?php if ($mensaje !== ""): ?>

        <div class="alert alert-<?php echo $tipoMensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>

    <?php endif; ?>


    <!-- FORMULARIO DE REGISTRO -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                Registrar nuevo usuario
            </h5>

        </div>


        <div class="card-body">

            <form method="POST">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Nombre de usuario
                        </label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            maxlength="50"
                            required
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            maxlength="100"
                            required
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            minlength="8"
                            required
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Rol del usuario
                        </label>

                        <select
                            name="role_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Seleccionar rol
                            </option>

                            <?php while ($rol = $roles->fetch_assoc()): ?>

                                <option value="<?php echo $rol["id"]; ?>">

                                    <?php echo htmlspecialchars($rol["name"]); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary mt-4"
                >
                    Registrar usuario
                </button>

            </form>

        </div>

    </div>


    <!-- TABLA DE USUARIOS -->

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                Usuarios registrados
            </h5>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-primary">

                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($usuario = $usuarios->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $usuario["id"]; ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($usuario["username"]); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($usuario["email"]); ?>
                                </td>


                                <td>

                                    <span class="badge bg-primary">

                                        <?php echo htmlspecialchars($usuario["rol"]); ?>

                                    </span>

                                </td>


                                <td>

                                    <?php if ($usuario["id"] != $_SESSION["id"]): ?>

                                        <!-- BOTÓN EDITAR -->

                                        <a
                                            href="editar_usuario.php?id=<?php echo $usuario['id']; ?>"
                                            class="btn btn-warning btn-sm mb-1"
                                        >
                                            Editar
                                        </a>


                                        <!-- BOTÓN ELIMINAR -->

                                        <form
                                            action="eliminar_usuario.php"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo $usuario["id"]; ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
                                            >
                                                Eliminar
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            Tu cuenta
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
