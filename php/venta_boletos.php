<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Venta de boletos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/boletos.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<!-- Modal de advertencia -->
<!-- Modal de resumen -->
<div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="summaryModalLabel">Resumen de tu compra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p><strong>Tipo de boleto:</strong> <span id="modal-ticket-type"></span></p>
        <p><strong>Asientos seleccionados:</strong> <span id="modal-seats"></span></p>
        <p><strong>Precio por asiento:</strong> $<span id="modal-price"></span></p>
        <p><strong>Total a pagar:</strong> $<span id="modal-total"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-success" id="confirm-purchase">Confirmar</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal de advertencia -->
<div class="modal fade" id="limitModal" tabindex="-1" aria-labelledby="limitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="limitModalLabel">Límite alcanzado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        Solo puedes seleccionar hasta 3 asientos. Deselecciona uno para elegir otro.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>



<script>
    const urlParams = new URLSearchParams(window.location.search);
    const registro = urlParams.get('registro');
    const asiento = urlParams.get('asiento');
    const error = urlParams.get('error');

    if (registro === 'exitoso') {
        Swal.fire({
            title: '¡Éxito!',
            text: `Asiento ${asiento} registrado correctamente 🎉`,
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            // Limpiar los parámetros de la URL sin recargar
            window.history.replaceState({}, document.title, window.location.pathname);
            location.reload();
        });
    } else if (registro === 'fallido') {
        Swal.fire({
            title: 'Error',
            text: `No se pudo registrar el asiento. ${error}`,
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }
</script>



<body>

    
<?php
$id_funcion = $_GET['id_funcion'] ?? null;
$nombre_pelicula = $_GET['nombre'] ?? 'Película desconocida';
$sala = $_GET['sala'] ?? 'S/N';
$hora = $_GET['hora'] ?? '00:00';
$funcion = $sala . ' - ' . $hora;
?>


    <div class="container mt-5 pt-5 text-center">
        <h2 class="movie-title mb-3"><?= htmlentities($nombre_pelicula) ?></ /h2>

            <p class="lead text-secondary">
                <strong>Sala:</strong> <?= htmlentities($sala) ?> &nbsp;|&nbsp;<strong>Horario:</strong> <?= htmlentities($hora) ?> &nbsp;|&nbsp;
                <strong>Función:</strong> <?= htmlentities($funcion) ?>
            </p>

    </div>
    
<!-- Div oculto para pasar id_funcion a JS -->
<div id="funcion-data" data-id-funcion="<?= htmlentities($id_funcion) ?>"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Cinema</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasDarkNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end text-bg-dark" id="offcanvasDarkNavbar">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">¿Qué estás buscando?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item"><a class="nav-link active" href="index.html">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Ver nuestros horarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Alimentos</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5 pt-5 text-center">
        <h2>Selecciona tus asientos</h2>

        <div class="cs-screen">Pantalla</div>


        <?php
        // Conexión a Oracle
        include("conection.php");

        // Consulta de asientos
        $sql = "SELECT ID_ASIENTO FROM ASIENTO ORDER BY ID_ASIENTO";

        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        ?>


        <div class="cs-seat-map">
            <!-- Este es un fragmento. El archivo completo ya lo tienes y solo necesitas integrar lo siguiente: -->

<?php
include("conection.php");
$id_funcion = $_GET['id_funcion'] ?? 1;

// Obtener asientos ocupados
$sql_ocupados = "SELECT ID_ASIENTO FROM FUNCION_ASIENTO WHERE ID_FUNCION = :id_funcion";
$stid_ocupados = oci_parse($conn, $sql_ocupados);
oci_bind_by_name($stid_ocupados, ":id_funcion", $id_funcion);
oci_execute($stid_ocupados);

$asientos_ocupados = [];
while ($row = oci_fetch_assoc($stid_ocupados)) {
    $asientos_ocupados[] = $row['ID_ASIENTO'];
}
oci_free_statement($stid_ocupados);
?>

<!-- En la sección de impresión de botones de asiento -->
<?php
$contador = 0;
echo '<div class="cs-seat-row">';
while ($row = oci_fetch_assoc($stid)) {
    $id = $row['ID_ASIENTO'];
    $ocupado = in_array($id, $asientos_ocupados) ? 'class="cs-seat cs-occupied" disabled' : 'class="cs-seat cs-available"';

    echo '<button ' . $ocupado . ' data-seat="' . $id . '" aria-label="Asiento ' . $id . '"></button>';
    $contador++;
    if ($contador % 5 == 0) {
        echo '</div><div class="cs-seat-row">';
    }
}
echo '</div>';
?>



        </div>



        <?php
        oci_free_statement($stid);
        oci_close($conn);
        ?>


        <?php
        // Conexión a Oracle
        include("conection.php");

        // Consulta de tipos de boleto
        $sql = "SELECT ID_TIPO, PRECIO FROM TIPO_BOLETO ORDER BY ID_TIPO";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        ?>

        <div class="container mt-4">
                <label for="tipo-boleto" class="form-label"><strong>Selecciona el tipo de boleto:</strong></label>
                <select id="tipo-boleto" class="form-select">
                        <?php while ($row = oci_fetch_assoc($stid)): ?>
                                <?php

                                $id_tipo = (int)$row['ID_TIPO'];

                                if ($id_tipo === 1) {
                                    $nombre = 'Básico';
                                } elseif ($id_tipo === 2) {
                                    $nombre = 'Cliente';
                                } elseif ($id_tipo === 3) {
                                    $nombre = 'Estrella';
                                } else {
                                    $nombre = 'Desconocido';
                                }

                                ?>
                                <option value="<?= $row['ID_TIPO'] ?>" data-precio="<?= $row['PRECIO'] ?>">
    <?= $nombre ?> - $<?= $row['PRECIO'] ?>
</option>

                            <?php endwhile; ?>
                    </select>
        </div>

        <?php
        oci_free_statement($stid);
        oci_close($conn);
        ?>



        <!-- Resumen -->
        <div class="cs-summary">
            <p>Total de asientos seleccionados: <span id="selected-seats">0</span></p>
            <p>Asientos seleccionados: <span id="selected-seat-list">Ninguno</span></p>
            <button id="confirm-button" class="btn btn-primary">Confirmar compra</button>

            

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/boletos.js"></script>
</body>

</html>