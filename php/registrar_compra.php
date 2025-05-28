<?php
include('conection.php');

$id_funcion = $_GET['id_funcion'];
$id_asiento = $_GET['id_asiento'];

// Aquí deberías insertar el asiento en la base de datos
// Por ejemplo:
$sql = "INSERT INTO FUNCION_ASIENTO (ID_FUNCION, ID_ASIENTO) VALUES (:id_funcion, :id_asiento)";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":id_funcion", $id_funcion);
oci_bind_by_name($stid, ":id_asiento", $id_asiento);

if (oci_execute($stid)) {
    // Redirigir con parámetros
    $params = http_build_query([
        'registro' => 'exitoso',
        'asiento' => $id_asiento
    ]);
    header("Location: venta_boletos.php?$params");
    exit;
} else {
    $e = oci_error($stid);
    $params = http_build_query([
        'registro' => 'fallido',
        'error' => $e['message']
    ]);
    header("Location: venta_boletos.php?$params");
    exit;
}

oci_free_statement($stid);
oci_close($conn);
?>
