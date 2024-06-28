<?php

$servername = "localhost";
$username = "root";
$password = "Tsuki07$-";
$dbname = "distribucion_de_panes1";
$port = 3306;


$conn = new mysqli($servername, $username, $password, $dbname, $port);


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


$nombre_pan = $_POST['nombre_pan'];
$precio = $_POST['precio'];
$cantidad = $_POST['cantidad'];
$fecha_creacion = $_POST['fecha_creacion'];
$fecha_entrega = $_POST['fecha_entrega'];
$sucursal = $_POST['sucursal'];


$sql = "INSERT INTO pedidos (Nombre_del_pan, Precio, Cantidad, Fecha_elaboracion, Fecha_entrega, Sucursal) VALUES ('$nombre_pan', $precio, $cantidad, '$fecha_creacion', '$fecha_entrega', '$sucursal')";

if ($conn->query($sql) === TRUE) {
   
    header("Location: inicio.php");
    exit();
} else {
 
    echo "Ha ocurrido un error Por favor, inténtelo de nuevo";
    echo '<br><button type="button" class="btn btn-danger" onclick="window.location.href=\'index.html\'">Reintentar</button>';
}

$conn->close();
?>
