<?php

$server_name = "hr"; // O el usuario que corresponda
$server_pass = "hr"; // O la contraseña que corresponda
$server_conn_str = "localhost/XEPDB1"; // String de conexión

$mensaje = ""; // Variable para almacenar mensajes de éxito o error
$upload_dir_poster = "../img/posters/"; // Directorio para guardar pósters en el sistema de archivos
$upload_dir_trailer = "../videos/trailers/"; // Directorio para guardar tráilers en el sistema de archivos

// Crear directorios si no existen (opcional, pero buena práctica)
if (!is_dir($upload_dir_poster)) {
    mkdir($upload_dir_poster, 0777, true);
}
if (!is_dir($upload_dir_trailer)) {
    mkdir($upload_dir_trailer, 0777, true);
}

// Conexión a la base de datos
$conn = oci_connect($server_name, $server_pass, $server_conn_str);

if (!$conn) {
    $e = oci_error();
    $mensaje = "Error de conexión a la base de datos: " . htmlentities($e['message']);
    // Mostrar el formulario incluso si hay error de conexión inicial, o manejar como prefieras
} else {
    // Procesar el formulario de inserción
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_pelicula = $_POST["id_pelicula"];
        $nombre = $_POST["nombre"];
        $resumen = $_POST["resumen"];
        $clasificacion = $_POST["clasificacion"];
        $duracion = $_POST["duracion"];
        $genero = $_POST["genero"];

        // --- INICIO Manejo de BLOBs ---
        // La tabla se llama PELICULA (mayúsculas)
        $sql = "INSERT INTO PELICULA (id_pelicula, nombre, resumen, clasificacion, duracion, genero, poster, trailer)
                VALUES (:id_pelicula, :nombre, :resumen, :clasificacion, :duracion, :genero, EMPTY_BLOB(), EMPTY_BLOB())
                RETURNING poster, trailer INTO :poster, :trailer";

        $stmt = oci_parse($conn, $sql);

        // Descriptores LOB para poster y trailer
        $poster_blob = oci_new_descriptor($conn, OCI_D_LOB);
        $trailer_blob = oci_new_descriptor($conn, OCI_D_LOB);

        // Enlazar variables PHP a los placeholders de Oracle
        oci_bind_by_name($stmt, ":id_pelicula", $id_pelicula);
        oci_bind_by_name($stmt, ":nombre", $nombre);
        oci_bind_by_name($stmt, ":resumen", $resumen);
        oci_bind_by_name($stmt, ":clasificacion", $clasificacion);
        oci_bind_by_name($stmt, ":duracion", $duracion);
        oci_bind_by_name($stmt, ":genero", $genero);
        oci_bind_by_name($stmt, ":poster", $poster_blob, -1, OCI_B_BLOB); // Enlazar el descriptor LOB
        oci_bind_by_name($stmt, ":trailer", $trailer_blob, -1, OCI_B_BLOB); // Enlazar el descriptor LOB

        // Ejecutar la sentencia sin auto-commit para manejar la carga de BLOBs
        if (oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
            $poster_guardado_db = false;
            $trailer_guardado_db = false;
            $error_archivo = false;

            // Procesar y guardar el PÓSTER en el BLOB
            if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] == UPLOAD_ERR_OK) {
                if (is_uploaded_file($_FILES["poster"]["tmp_name"])) {
                    if ($poster_blob->savefile($_FILES["poster"]["tmp_name"])) {
                        $poster_guardado_db = true;
                        // Opcional: Mover también al sistema de archivos si lo necesitas
                        // move_uploaded_file($_FILES["poster"]["tmp_name"], $upload_dir_poster . $_FILES["poster"]["name"]);
                    } else {
                        $mensaje .= " Error al guardar el póster en la BD.";
                        $error_archivo = true;
                    }
                }
            } elseif (isset($_FILES["poster"]) && $_FILES["poster"]["error"] != UPLOAD_ERR_NO_FILE) {
                $mensaje .= " Error al subir el archivo del póster: Código " . $_FILES["poster"]["error"];
                $error_archivo = true;
            } else {
                 $poster_guardado_db = true; // Considerar éxito si el póster no era obligatorio y no se subió
            }


            // Procesar y guardar el TRÁILER en el BLOB (opcional)
            if (isset($_FILES["trailer"]) && $_FILES["trailer"]["error"] == UPLOAD_ERR_OK) {
                if (is_uploaded_file($_FILES["trailer"]["tmp_name"])) {
                    if ($trailer_blob->savefile($_FILES["trailer"]["tmp_name"])) {
                        $trailer_guardado_db = true;
                        // Opcional: Mover también al sistema de archivos
                        // move_uploaded_file($_FILES["trailer"]["tmp_name"], $upload_dir_trailer . $_FILES["trailer"]["name"]);
                    } else {
                        $mensaje .= " Error al guardar el tráiler en la BD.";
                        $error_archivo = true;
                    }
                }
            } elseif (isset($_FILES["trailer"]) && $_FILES["trailer"]["error"] != UPLOAD_ERR_NO_FILE) {
                // Error si se intentó subir tráiler pero falló por otra razón que no sea "no se subió archivo"
                $mensaje .= " Error al subir el archivo del tráiler: Código " . $_FILES["trailer"]["error"];
                $error_archivo = true;
            } else {
                // Si no se subió tráiler (UPLOAD_ERR_NO_FILE) y es opcional, se considera éxito para esta parte
                $trailer_guardado_db = true;
            }

            if ($poster_guardado_db && $trailer_guardado_db && !$error_archivo) {
                oci_commit($conn); // Confirmar la transacción si todo fue bien
                $mensaje = "Película insertada con éxito en la base de datos.";
            } else {
                oci_rollback($conn); // Revertir si hubo problemas con los BLOBs
                if (empty($mensaje)) { // Asegurar que haya un mensaje de error
                    $mensaje = "Error al procesar los archivos para la base de datos.";
                }
            }
        } else {
            $e = oci_error($stmt);
            $mensaje = "Error al ejecutar la inserción: " . htmlentities($e['message']);
        }

        // Liberar descriptores LOB y statement
        if ($poster_blob) $poster_blob->free();
        if ($trailer_blob) $trailer_blob->free();
        if ($stmt) oci_free_statement($stmt);
        // --- FIN Manejo de BLOBs ---
    }
    // Cerrar conexión solo si se abrió correctamente
    if ($conn) {
        oci_close($conn);
    }
}

// Mostrar el mensaje (puedes integrarlo mejor en tu HTML)
if (!empty($mensaje)) {
    echo "<p>" . htmlspecialchars($mensaje) . "</p>";
    echo "<p><a href='insertMovies.html'>Volver al formulario</a></p>"; // Cambia 'tu_formulario.html' por el nombre de tu archivo de formulario
}

?>