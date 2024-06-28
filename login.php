<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $username_input = $_POST['username'];
    $password_input = $_POST['password'];

    // Conectar a la base de datos (reemplaza con tus datos de conexión)
    $servername = "localhost";
    $db_username = "root";
    $db_password = "Tsuki07$-";
    $dbname = "distribucion_de_panes1";

    // Crear conexión
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para verificar las credenciales en la base de datos
    $sql = "SELECT * FROM usuarios WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_input, $password_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Usuario y contraseña coinciden
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];

        // Redireccionar según el usuario
        if ($row['username'] == 'Johnny' || $row['username'] == 'Alexis') {
            header("Location: inicio.php");
        } else {
            header("Location: vista_usuario.php");
        }
        exit();
    } else {
        // Credenciales incorrectas, mostrar alerta
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
        // Redirigir de vuelta a inicio.php después de mostrar el alerta
        echo "<script>window.location.href = 'inicio.php';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
