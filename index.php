<?php
session_start();

// Verificar si ya hay una sesión activa
if (isset($_SESSION['username'])) {
    // Si el usuario es Johnny o Alexis, redirigir a inicio.php
    if ($_SESSION['username'] == 'Johnny' || $_SESSION['username'] == 'Alexis') {
        header("Location: inicio.php");
    } else {
        header("Location: vista_usuario.php");
    }
    exit();
}

// Verificar cookies para iniciar sesión automática si está marcado "Recuérdame"
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    // Conectar a la base de datos para validar las cookies
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

    // Validar las cookies en la base de datos
    $username_cookie = $_COOKIE['username'];
    $password_cookie = $_COOKIE['password'];
    $sql = "SELECT * FROM usuarios WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_cookie, $password_cookie);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Iniciar sesión con el usuario válido
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];

        // Redireccionar según el usuario
        if ($row['username'] == 'Johnny' || $row['username'] == 'Alexis') {
            header("Location: inicio.php");
        } else {
            header("Location: vista_usuario.php");
        }
        exit();
    }
    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
         .input-field .input {
            height: 45px;
            width: 85%;
            border: none;
            outline: none;
            border-radius: 30px;
            color: #fff;
            padding: 0 0 0 42px;
            background: rgba(255, 255, 255, 0.1);
        }
        i {
            position: relative;
            top: -31px;
            left: 17px;
            color: #fff;
        }
        ::-webkit-input-placeholder {
            color: #fff;
        }
        span {
            color: #fff;
            font-size: small;
            display: flex;
            justify-content: center;
            padding: 10px 0 15px 0;
        }
        header {
            color: #fff;
            font-size: 30px;
            display: flex;
            justify-content: center;
            padding: 0 0 15px 0;
        }
        .input-field {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            position: relative;
        }
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-image: url("pansitos.gif");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90vh;
        }
        .container {
            width: 350px;
            display: flex;
            flex-direction: column;
            padding: 15px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 15px;
        }
        .submit {
            height: 45px;
            width: 100%;
            border: none;
            outline: none;
            border-radius: 30px;
            background: #fff;
            color: #000;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit:hover {
            background: #ddd;
        }
        .bottom {
            display: flex;
            justify-content: space-between;
            color: #fff;
            font-size: 12px;
        }
        .bottom a {
            color: #fff;
            text-decoration: none;
        }
        .bottom a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>
    <div class="box">
        <div class="container">
            <div class="top-header">
                <span>Login</span>
                <header>Iniciar sesión</header>
            </div>

        <form action="login.php" method="post">
            <div class="input-field">
                <input type="text" id="username" name="username" class="input" placeholder="Usuario" required>
                <i class='bx bx-user'></i>
                <br>
            </div>
            <div class="input-field">
                <input type="password" id="password" name="password" class="input" placeholder="Contraseña" required>
                <i class='bx bx-lock-alt'></i>
                <br>
            </div>
            <div class="input-field">
                <input type="submit" class="submit" value="Iniciar Sesión">
            </div>
            <div class="bottom">
                <div class="left">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="check">Recuérdame</label>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>