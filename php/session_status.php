
<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario'])) {
    echo json_encode([
        'loggedIn' => true,
        'usuario' => $_SESSION['usuario'],
        'nombre' => $_SESSION['nombre'],
        'apellido' => $_SESSION['apellido'],
        'correo' => $_SESSION['correo']
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
