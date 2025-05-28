<?php

$server_name = "hr";
$server_pass = "hr";
$server_conn_str = "localhost/XEPDB1";

$pelicula_info_html = ""; // Variable para almacenar la información de la película
$pelicula_imagen_html = ""; // Variable para almacenar la imagen de la película
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && !empty(trim($_GET['id']))) 
{
    $pelicula = trim($_GET['id']);

    $conn = oci_connect($server_name, $server_pass, $server_conn_str);

    if (!$conn) {
        $e = oci_error();
        $mensaje_error = "Error de conexión: " . htmlentities($e['message']);
    } else {

        $sql = "SELECT * FROM PELICULA WHERE ID_PELICULA = :pelicula";
        $stid = oci_parse($conn, $sql);

        oci_bind_by_name($stid, ':pelicula', $pelicula);

        if(oci_execute($stid)) 
        {
            if ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) 
            {
                // Extraer información de la película
                $pelicula .= '<h1 class="title"> Información de la Película 🎬</h1>';
                $pelicula .= '<img class="movieImage" src="mostrar_blob.php?id=' . urlencode($row['ID_PELICULA']) . '&tipo=POSTER" alt="">';
                $pelicula .= '<ul>';
                $pelicula .= '<li class = "res"><strong>ID:</strong>' . htmlentities($row['ID_PELICULA']) . '</li>';
                $pelicula .= '<li class = "res"><strong>Título:</strong>' . htmlentities($row['NOMBRE']) . '</li>';
                $pelicula .= '<li class = "res"><strong>Resumen:</strong>' . htmlentities($row['RESUMEN']) . '</li>';
                $pelicula .= '<li class = "res"><strong>Clasificación:</strong>' . htmlentities($row['CLASIFICACION']) . '</li>';
                $pelicula .= '<li class = "res"><strong>Duración:</strong>' . htmlentities($row['DURACION']) . '</li>';
                $pelicula .= '<li class = "res"><strong>Género:</strong>' . htmlentities($row['GENERO']) . '</li>';
                $pelicula .= '</ul>';
                $pelicula .= '<video class="trailer" controls>
                                <source src="mostrar_blob.php?id=' . urlencode($row['ID_PELICULA']) . '&tipo=TRAILER" type="video/mp4">
                                Tu navegador no soporta el elemento de video.
                            </video>';
            }
            else 
            {
                $mensaje_error = "😕 No se encontró ninguna película con el ID: " . htmlentities($pelicula);
            }
        } else {
            $e = oci_error($stid);
            $mensaje_error = "Error al realizar la búsqueda: " . htmlentities($e['message']);
        }
        oci_free_statement($stid);
        oci_close($conn);
    }
}
elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['telefono_buscar']) && empty(trim($_GET['telefono_buscar']))) {
    $mensaje_error = "Por favor, ingrese un número de teléfono para buscar.";
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET['telefono_buscar'])) {
    // No hacer nada si no se ha enviado el parámetro, para que se muestre el formulario inicial.
    // O podrías redirigir: header("Location: buscar.html"); exit;
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultados de Busqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column; /* Para apilar el formulario y los resultados */
            align-items: center;
            justify-content: center;
            min-height: 90vh;
            padding-top: 60px; /* Para evitar que el contenido quede oculto detrás de la navbar fija */
        }
        .container, .resultados-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 650px;
            width: 100%; /* Para asegurar que ocupe el max-width */
            margin-bottom: 20px; /* Espacio entre contenedores */
        }
        .movieImage {
            margin-left: 16%;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        video {
            border-radius: 8px;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="tel"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .error {
            padding: 10px;
            margin-top: 15px;
            border-radius: 4px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        .res {
            background-color: #e9ecef;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 4px;
            border-left: 5px solid #007bff;
        }
        .res strong {
            color: #0056b3;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .enlace-registro {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
  </head>
  <body>

  <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Cinema</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">¿Qué estás buscando?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Iniciar Sesión</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Ver nuestros horarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../html/consultarPelicula.html">Consultar Peliculas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../html/consultarUsuario.html">Consultar Clientes</a>
                </li>
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
                </ul>
                <form class="d-flex mt-3" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
                    <button class="btn btn-success" type="submit">Search</button>
                </form> -->
            </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Buscar Usuario por ID 🔎</h2>
        <form action="buscar_pelicula.php" method="GET">
            <div>
                <label for="id_buscar">ID de Pelicula:</label>
                <input type="tel" id="id_buscar" name="id" required value="<?php echo isset($_GET['id']) ? htmlentities($_GET['id']) : ''; ?>">
            </div>
            <input type="submit" value="Buscar Pelicula">
        </form>
    </div>

    <?php if (!empty($pelicula) || !empty($mensaje_error)): ?>
        <div class="resultados-container">
            <?php if (!empty($mensaje_error)): ?>
                <div class="error">
                    <?php echo $mensaje_error; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($pelicula)): ?>
                <div>
                    <?php echo $pelicula; ?>
                </div>
            <?php endif; ?>
            <p style="text-align:center; margin-top:20px;"><a href="../html/consultarUsuario.html">🔍 Realizar otra búsqueda</a></p>
        </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>