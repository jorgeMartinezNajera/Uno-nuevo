<?php

$server_name = "hr";
$server_pass = "hr";
$server_conn_str = "localhost/XEPDB1"; 

$conn = oci_connect($server_name, $server_pass, $server_conn_str);

if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
} else{
    echo "Conexión exitosa a la base de datos Oracle.<br>";
}

?>