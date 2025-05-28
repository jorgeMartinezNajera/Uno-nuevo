
<?php
header('Content-Type: application/json');
include('conection.php');

$id_funcion = $_GET['id_funcion'];

$sql = "SELECT ID_ASIENTO FROM FUNCION_ASIENTO WHERE ID_FUNCION = :id_funcion";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":id_funcion", $id_funcion);
oci_execute($stid);

$asientos_ocupados = [];
while ($row = oci_fetch_assoc($stid)) {
    $asientos_ocupados[] = $row['ID_ASIENTO'];
}

oci_free_statement($stid);
oci_close($conn);

echo json_encode($asientos_ocupados);
?>
