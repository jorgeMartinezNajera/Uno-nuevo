<?php
$server_name = "hr";
$server_pass = "hr";
$server_conn_str = "localhost/XEPDB1";

if (!isset($_GET['id']) || !isset($_GET['tipo'])) {
    http_response_code(400);
    exit("Parámetros inválidos.");
}

$id = $_GET['id'];
$tipo = $_GET['tipo']; // 'imagen' o 'trailer'

$conn = oci_connect($server_name, $server_pass, $server_conn_str);
if (!$conn) {
    http_response_code(500);
    exit("Error de conexión a la BD.");
}

$sql = "SELECT $tipo FROM PELICULA WHERE ID_PELICULA = :id";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":id", $id);
oci_execute($stid);

$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_LOBS);
if (!$row || empty($row[$tipo])) {
    http_response_code(404);
    exit("Archivo no encontrado.");
}

$blob = $row[$tipo];

// Detectar tipo MIME (esto es opcional, tú defines el tipo según el contenido)
if ($tipo === 'imagen') {
    header("Content-Type: image/jpeg");
} elseif ($tipo === 'trailer') {
    header("Content-Type: video/mp4");
}

echo $blob;

oci_free_statement($stid);
oci_close($conn);
?>
