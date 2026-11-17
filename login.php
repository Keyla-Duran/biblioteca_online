
<?php
session_start();

require_once "config/conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT users.id, users.username,
                   users.email, users.password,
                   roles.name AS rol
            FROM users
            INNER JOIN roles
            ON users.role_id = roles.id
            WHERE users.email = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["password"])) {

            session_regenerate_id(true);

            $_SESSION["id"] = $usuario["id"];
            $_SESSION["username"] = $usuario["username"];
            $_SESSION["rol"] = $usuario["rol"];

            if ($usuario["rol"] == "Administrador") {
                header("Location: admin/dashboard.php");
                exit();

            } elseif ($usuario["rol"] == "Bibliotecario") {
                header("Location: bibliotecario/dashboard.php");
                exit();

            } else {
                header("Location: lector/catalogo.php");
                exit();
            }

        } else {
            $error = "Contraseña incorrecta.";
        }

    } else {
        $error = "El correo no está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Biblioteca Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css?v=2">
</head>

<body class="login-body">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-5">

            <div class="card shadow-lg border-0 rounded-4">

                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <h2 class="fw-bold"> Biblioteca Online</h2>
                        <p class="text-muted">Bienvenido a nuestra biblioteca</p>
                    </div>

                    <?php if ($error != ""): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>

                            <input type="email"
                            name="email"
                            class="form-control form-control-lg"
                            placeholder="Ingrese su correo"
                            required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Contraseña</label>

                            <input type="password"
                            name="password"
                            class="form-control form-control-lg"
                            placeholder="Ingrese su contraseña"
                            required>
                        </div>

                        <button type="submit"
                        class="btn btn-primary btn-lg w-100">
                            Iniciar sesión
                        </button>

                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Sistema de Gestión de Biblioteca
                        </small>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
