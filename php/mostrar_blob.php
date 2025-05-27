<?php
$server_name = "hr";
$server_pass = "hr";
$server_conn_str = "localhost/XEPDB1";

if (!isset($_GET['id']) || !isset($_GET['tipo'])) {
    http_response_code(400);
    exit("Parámetros inválidos.");
}

$id = $_GET['id'];
$tipo_from_url = trim($_GET['tipo']); // Ej: "poster" o "trailer"

$column_name_in_db = "";
$content_type_header = "";

// Determinar la columna y el Content-Type basado en el parámetro 'tipo'
// Usamos strcasecmp para comparación insensible a mayúsculas/minúsculas
if (strcasecmp($tipo_from_url, "poster") == 0) {
    $column_name_in_db = "POSTER"; // Nombre exacto de la columna en tu BD
    $content_type_header = "image/jpg"; // Asume que tus pósters son JPEG. Cambia a image/png, image/gif si es necesario.
} elseif (strcasecmp($tipo_from_url, "trailer") == 0) {
    $column_name_in_db = "TRAILER"; // Nombre exacto de la columna en tu BD
    $content_type_header = "video/mp4"; // Asume que tus tráilers son MP4
} else {
    http_response_code(400);
    exit("Tipo de archivo no válido: " . htmlentities($tipo_from_url));
}

$conn = oci_connect($server_name, $server_pass, $server_conn_str);
if (!$conn) {
    error_log("Error de conexión a la BD en mostrar_blob.php: " . oci_error()['message']);
    http_response_code(500);
    exit("Error de conexión a la BD.");
}

// Muy importante: Enviar la cabecera Content-Type ANTES de cualquier otra salida.
header("Content-Type: " . $content_type_header);

$sql = "SELECT $column_name_in_db FROM PELICULA WHERE ID_PELICULA = :id";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":id", $id);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    // No envíes http_response_code aquí si ya enviaste la cabecera Content-Type,
    // ya que el cuerpo del error podría interferir. Loguea el error.
    error_log("Error al ejecutar consulta en mostrar_blob.php: " . $e['message'] . " (SQL: $sql, ID: $id)");
    // Podrías intentar salir de una forma que indique un error de imagen/video,
    // pero es complejo una vez que la cabecera de contenido ya fue enviada.
    // Por ahora, un log es lo más limpio.
    exit; // Salir si la consulta falla
}

$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_LOBS);

if (!$row || empty($row[$column_name_in_db])) {
    // Similar al caso de error de ejecución, loguear el error.
    error_log("Archivo no encontrado en mostrar_blob.php para ID: $id, Columna: $column_name_in_db");
    // http_response_code(404); // No enviar si la cabecera Content-Type ya fue enviada
    exit; // Salir si no se encuentra el BLOB
}

$blob = $row[$column_name_in_db];

// La única salida después de las cabeceras debe ser el contenido del BLOB.
echo $blob;

oci_free_statement($stid);
oci_close($conn);
?>