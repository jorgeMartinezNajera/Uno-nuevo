<?php

$server_name = "hr";
$server_pass = "hr";
$server_conn_str = "localhost/XEPDB1";

$pelicula_info_html = ""; // Variable para almacenar la información de la película
$pelicula_imagen_html = ""; // Variable para almacenar la imagen de la película
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['pelicula_buscar']) && !empty(trim($_GET['pelicula_buscar']))) 
{
    $pelicula_buscar = trim($_GET['pelicula_buscar']);

    $conn = oci_connect($server_name, $server_pass, $server_conn_str);

    if (!$conn) {
        $e = oci_error();
        $mensaje_error = "Error de conexión: " . htmlentities($e['message']);
    } else {
    
        $sql = "SELECT * FROM PELICULA WHERE ID_PELICULA = :pelicula_buscar";
        $stid = oci_parse($conn, $sql);

        oci_bind_by_name($stid, ':pelicula_buscar', $pelicula_buscar);

        if(oci_execute($stid)) 
        {
            if ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) 
            {
                // Extraer información de la película
                $pelicula_imagen_html .= 
                "
                <div class='col-5 img-container'>
                    <img class='movieImage' src='mostrar_blob.php?id=" . urlencode($row['ID_PELICULA']) . "&tipo=POSTER' alt=''>
                </div>
                ";
                //Sección de descripción
                $pelicula_info_html .= "
                    <div class='col-7 context'>
                        <div class='title-container'>
                            <h1 class='title'>" . htmlentities($row['NOMBRE']) . "</h1>
                        </div>
                        <div class='sinopsis'>
                            <p class='d-inline-flex gap-1'>
                                <a class='drop-sinopsis' data-bs-toggle='collapse' href='#collapseExample' role='button' aria-expanded='false' aria-controls='collapseExample'>
                                Sinopsis 📕
                                </a>
                            </p>
                            <div class='collapse' id='collapseExample'>
                                <div class='card card-body'>"
                                    . htmlentities($row['RESUMEN']) .
                                    "<div class='trailer-container'>
                                    <video class='trailer' controls>
                                        <source src='mostrar_blob.php?id=" . urlencode($row['ID_PELICULA']) . "&tipo=TRAILER' type='video/mp4'>
                                        Tu navegador no soporta el elemento de video.
                                    </video>
                                </div>
                            </div>
                        </div>
                        <div class='salas'>
                            <div class='salaNormal'>
                                <h2>Sala Normal</h2>
                                <!-- <h5>Sala 1</h5> -->
                                <div class='horarios'>
                                    <a href='./venta_boletos.html' class='hora'>9:00</a>
                                    <a href='./venta_boletos.html' class='hora'>9:50</a>
                                    <a href='./venta_boletos.html' class='hora'>10:20</a>
                                    <a href='./venta_boletos.html' class='hora'>12:40</a>
                                    <a href='./venta_boletos.html' class='hora'>14:00</a>
                                    <a href='./venta_boletos.html' class='hora'>14:50</a>
                                </div>
                                <!-- <h5>Sala 2</h5> -->
                                <div class='horarios'>
                                    <a href='./venta_boletos.html' class='hora'>15:20</a>
                                    <a href='./venta_boletos.html' class='hora'>16:00</a>
                                    <a href='./venta_boletos.html' class='hora'>16:10</a>
                                    <a href='./venta_boletos.html' class='hora'>18:20</a>
                                </div>
                            </div>
                            <div class='salaVIP'>
                                <h2>Sala VIP</h2>
                                <div class='horarios'>
                                    <a href='./venta_boletos.html' class='hora'>12:40</a>
                                    <a href='./venta_boletos.html' class='hora'>14:00</a>
                                    <a href='./venta_boletos.html' class='hora'>16:00</a>
                                    <a href='./venta_boletos.html' class='hora'>16:10</a>
                                </div>
                            </div>
                        </div>
                    </div>";
            }
            else {
                $mensaje_error = " 😭 No se encontró la película con ID: " . htmlentities($pelicula_buscar);
            }
        }
        else {
            $e = oci_error($stid);
            $mensaje_error = "Error al ejecutar la consulta: " . htmlentities($e['message']);
        }

        oci_free_statement($stid);
        oci_close($conn);

    }
} elseif($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['pelicula_buscar']) && empty(trim($_GET['pelicula_buscar']))) {
    $mensaje_error = "Por favor, ingresa un ID de película válido.";
} else {
    $mensaje_error = "Método de solicitud no válido.";
}
?>

<!doctype html>
<html lang="sp">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Función</title>
    <link rel="stylesheet" href="../css/funcion.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  </head>
  <body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="./index.php">Cinema</a>
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

    <div class="container funcion">
        <div class="row">
            <?php if (!empty($mensaje_error) || $pelicula_info_html): ?>
                <?php if (!empty($mensaje_error)): ?>
                    <div class="error">
                        <?php echo $mensaje_error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pelicula_info_html)): ?>
                        <?php echo $pelicula_imagen_html; ?>
                        <?php echo $pelicula_info_html; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>