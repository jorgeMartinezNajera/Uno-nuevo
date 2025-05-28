<?php
include("conection.php");

// Obtener datos JSON del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

$id_funcion = $data['id_funcion'];
$asientos = $data['asientos']; // array de ID_ASIENTO
var_dump($asientos);
if (isset($id_funcion)) {
    # code...
    echo "Recibe algo";
    var_dump($id_funcion);
}else{
    echo "No recibe nada";
}

foreach ($asientos as $id_asiento) {
    $sql = "INSERT INTO FUNCION_ASIENTO (ID_FUNCION, ID_ASIENTO) VALUES (:id_funcion, :id_asiento)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id_funcion", $id_funcion);
    oci_bind_by_name($stmt, ":id_asiento", $id_asiento);
    oci_execute($stmt);
}

oci_close($conn);
echo json_encode(["status" => "success"]);
?>
