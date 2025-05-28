<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CINETEC</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/index.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<script>
  function getQueryParams() {
    const params = new URLSearchParams(window.location.search);
    return {
      registro: params.get('registro'),
      login: params.get('login'),
      usuario: params.get('usuario'),
      nombre: params.get('nombre'),
      apellido: params.get('apellido'),
      logout: params.get('logout'),
      correo: params.get('correo')
    };
  }

  function mostrarSweetAlert() {
    const {
      registro,
      login,
      logout,
      usuario,
      nombre,
      apellido,
      correo
    } = getQueryParams();

    if (registro === 'exitoso') {
      Swal.fire({
        title: '¡Registro exitoso!',
        html: `Bienvenido/a <strong>${nombre} ${apellido}</strong><br>Tu correo registrado es: <strong>${correo}</strong><br>Nombre de usuario: <strong>${usuario}</strong>`,
        icon: 'success',
        confirmButtonText: 'Aceptar'
      }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    } else if (login === 'exitoso') {
      Swal.fire({
        title: '¡Inicio de sesión exitoso!',
        html: `Bienvenido/a de nuevo <strong>${nombre} ${apellido}</strong><br>Correo: <strong>${correo}</strong><br>Usuario: <strong>${usuario}</strong>`,
        icon: 'success',
        confirmButtonText: 'Continuar'
      }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    } else if (login === 'fallido') {
      Swal.fire({
        title: 'Error de inicio de sesión',
        text: 'Correo/usuario o contraseña incorrectos. Intenta nuevamente.',
        icon: 'error',
        confirmButtonText: 'Reintentar'
      }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    } else if (logout === 'exitoso') {
      Swal.fire({
        title: 'Sesión cerrada',
        text: 'Has cerrado sesión exitosamente.',
        icon: 'info',
        confirmButtonText: 'Aceptar'
      }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    }
  }


  function verificarSesion() {
    fetch('session_status.php')
      .then(res => res.json())
      .then(data => {
        if (data.loggedIn) {
          const loginLink = document.getElementById('loginLink');
          loginLink.innerHTML = `
            <span class="login-text"><strong>${data.usuario}</strong></span>
            <div class="user-icon">👤</div>
            <div class="dropdown-menu" id="dropdownMenu">
              <a href="historial.html">Historial</a>
              <a href="cerrar_sesion.php">Cerrar sesión</a>
            </div>
          `;
          loginLink.classList.add('logged-in');
          loginLink.onclick = () => {
            document.getElementById('dropdownMenu').classList.toggle('show');
          };
        }
      });
  }

  window.onload = () => {
    mostrarSweetAlert();
    verificarSesion();
  };
</script>



<body>
  <header>
    <div class="header-left">
      <img id="logo" src="../img/LogoCineFBDOscuro.png" alt="Logo CINETEC" />
      <h1 class="cinetech-title">
        <strong><span class="cine">Cine</span><span class="te">TEC</span><span class="ch">H</span></strong>
      </h1>
    </div>
    <div class="user-menu-container" id="userMenuContainer">
      <div class="login-link" onclick="toggleAuthPanel()" id="loginLink">
        <span class="login-text"><strong>Iniciar Sesión</strong></span>
        <div class="user-icon">👤</div>
      </div>
    </div>
  </header>

  <div class="auth-panel" id="authPanel">
    <form class="auth-form" id="authForm" action="create.php" method="POST">
      <h3 id="formTitle">Registro</h3>

      <!-- Campos de registro -->
      <div class="form-columns register-field">
        <div class="column">
          <input type="text" placeholder="Nombre de usuario" name="user">
          <input type="text" placeholder="Nombre(s)" name="name" />
          <input type="text" placeholder="Primer apellido" name="lastName" />
          <input type="text" placeholder="Segundo apellido" name="secondLastName" />
          <input type="email" placeholder="Correo" name="email" />
        </div>
        <div class="column">
          <input type="password" placeholder="Contraseña" name="password" />
          <input type="tel" placeholder="Teléfono" pattern="[0-9]{10}" title="Ingresa un número de 10 dígitos"
            name="phone" />
          <label for="membershipType">Tipo de membresía:</label>
          <select id="membershipType" name="membershipType">
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="basica">Normal</option>
            <option value="premium">Platino</option>
            <option value="vip">Diamante</option>
          </select>
          <label for="paymentMethod">Método de pago (tarjeta):</label>
          <input type="text" id="paymentMethod" placeholder="Número de tarjeta" pattern="[0-9]{16}"
            title="Número de tarjeta de 16 dígitos" name="paymentMethod" />
        </div>
      </div>

      <!-- Campos de inicio de sesión -->
      <div class="login-field" style="display: none;">
        <input type="text" placeholder="Correo o nombre de usuario" name="emailL" />
        <input type="password" placeholder="Contraseña" name="passwordL" />
      </div>

      <button type="submit">Enviar</button>
      <small onclick="toggleAuthMode()">¿Ya tienes una cuenta? <strong>Inicia sesión</strong></small>
    </form>
  </div>
  <?php
  include("conection.php");

  $query = 'SELECT ID_PELICULA, NOMBRE FROM PELICULA';

  $stid = oci_parse($conn, $query);
  oci_execute($stid);
  ?>

  <h2>Tendencias</h2>
  <div class="custom-carousel" id="customCarousel">
    <div class="carousel-inner">
      <?php

      // Consulta de los pósters
      $query = 'SELECT ID_PELICULA, NOMBRE FROM PELICULA';
      $stid = oci_parse($conn, $query);
      oci_execute($stid);

      // Generar los ítems del carrusel
      $first = true;
      while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $activeClass = $first ? 'active' : '';
        echo '<div class="carousel-item ' . $activeClass . '">';
        echo '<a href="funcion.php?pelicula_buscar=' . $row['ID_PELICULA'] . '"><img src=".mostrar_blob.php?id=' . $row['ID_PELICULA'] . '" alt="' . htmlspecialchars($row['NOMBRE']) . '"></a>';
        echo '</div>';
        $first = false;
      }

      oci_close($conn);
      ?>

    </div>
    <button class="carousel-control prev" onclick="prevSlide()">❮</button>
    <button class="carousel-control next" onclick="nextSlide()">❯</button>
  </div>

  <?php oci_close($conn); ?>



  <div class="category-section">
    <div class="category-carousel">
      <h2>Terror</h2>
      <div class="carousel">
        <a href="funcion.php?pelicula_buscar=1">
          <div class="poster" style="background-image: url('../img/destinoFinalVertical.jpg')">
          </div>
          <div class="poster-title">Destino Final: Legado de Sangre</div>
        </a>
        <a href="funcion.php?pelicula_buscar=68795">
          <div class="poster" style="background-image: url('../img/screamVertical.jpg')">
          </div>
          <div class="poster-title">Scream VI</div>
        </a>
      </div>
    </div>

    <div class="category-carousel">
      <h2>Aventura</h2>
      <div class="carousel">
        <a href="funcion.php?pelicula_buscar=75314">
          <div class="poster" style="background-image: url('../img/minecraftVertical.jpg')"></div>
          <div class="poster-title">Minecraft: La Película</div>
        </a>
        <a href="funcion.php?pelicula_buscar=25467">
          <div class="poster" style="background-image: url('../img/dragonVertical.jpg')">
          </div>
          <div class="poster-title">Cómo Entrenar a tu Dragón</div>
        </a>
      </div>
    </div>
  </div>


  <script src="../script/scripts.js"></script>
</body>

</html>