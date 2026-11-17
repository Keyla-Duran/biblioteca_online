
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Biblioteca Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css?v=2">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #fff0f6, #eee5ff);
        }

        .portada {
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .contenido {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(98, 65, 143, 0.15);
        }

        .imagen-portada {
            width: 100%;
            height: 100%;
            min-height: 430px;
            object-fit: cover;
        }

        .texto-portada {
            padding: 45px;
        }

        .texto-portada h1 {
            font-size: 42px;
            color: #62418f;
            font-weight: bold;
        }

        .texto-portada p {
            color: #806c88;
            font-size: 17px;
            line-height: 1.7;
        }

        .boton-iniciar {
            background: linear-gradient(100deg, #df6b9a, #9872d4);
            color: white;
            border: none;
            padding: 13px 30px;
            border-radius: 12px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }

        .boton-iniciar:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(152, 114, 212, 0.3);
        }

        .marca {
            color: #9872d4;
            font-weight: bold;
            letter-spacing: 2px;
            font-size: 13px;
        }

        footer {
            color: #987f9d;
            text-align: center;
            padding: 15px;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="container-fluid">
            <span class="navbar-brand">
                 Biblioteca Online
            </span>
        </div>
    </nav>

    <main class="portada">

        <div class="container">
            <div class="contenido row align-items-center">

                <div class="col-md-6">
                    <img
                        src="img/biblioteca.jpg"
                        alt="Interior de nuestra biblioteca"
                        class="imagen-portada"
                    >
                </div>

                <div class="col-md-6">
                    <div class="texto-portada">

                        <span class="marca">
                            TU ESPACIO DE LECTURA
                        </span>

                        <h1 class="mt-3">
                            Bienvenido a nuestra Biblioteca
                        </h1>

                        <p class="mt-4">
                            Descubre nuevas historias, explora nuestro
                            catálogo y disfruta de un mundo lleno de
                            conocimiento y aprendizaje.
                        </p>

                        <p>
                            Encuentra tu próximo libro favorito
                            y comienza una nueva aventura.
                        </p>

                        <a href="login.php" class="boton-iniciar mt-3">
                            Iniciar sesión →
                        </a>

                    </div>
                </div>

            </div>
        </div>

    </main>

    <footer>
        Biblioteca Online © 2026 | Todos los derechos reservados
    </footer>

</body>
</html>
