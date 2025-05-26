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

    $name = $_POST['name'];
    $lastName = $_POST['lastName'];
    $secondLastName = $_POST['secondLastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $paymentMethod = $_POST['paymentMethod'];
    $membershipType = $_POST['membershipType'];

    $sql = 'INSERT INTO CLIENTE (NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, TELEFONO, METODO_PAGO, ID_MEMBRESIA) VALUES ( :name, :lastName, :secondLastName, :email, :phone, :paymentMethod, :membershipType)';
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':name', $name);
    oci_bind_by_name($stid, ':lastName', $lastName);
    oci_bind_by_name($stid, ':secondLastName', $secondLastName);
    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':password', $password);
    oci_bind_by_name($stid, ':phone', $phone);
    oci_bind_by_name($stid, ':paymentMethod', $paymentMethod);
    oci_bind_by_name($stid, ':membershipType', $membershipType);

    $result = oci_execute($stid);

    if ($result) {
        echo "Cliente creado exitosamente.";
    } else {
        $e = oci_error($stid);
        echo "Error al crear el cliente: " . $e['message'];
    }

    oci_free_statement($stid);
    oci_close($conn);
?>