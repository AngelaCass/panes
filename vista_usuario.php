<?php
session_start();

// Verificar si no hay una sesión válida
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Verificar las credenciales del usuario en la base de datos
$db_host = 'localhost';
$db_username = 'root';
$db_password = 'Tsuki07$-';
$db_name = 'distribucion_de_panes1';

// Conexión a la base de datos
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Preparar la consulta para verificar las credenciales
$username = $_SESSION['username'];
$sql = "SELECT * FROM usuarios WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró un usuario válido
if ($result->num_rows == 0) {
    // Si no se encontró el usuario en la base de datos, redirigir a la página de inicio
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre_pan"])) {
    if (isset($_POST["eliminar"])) {
        $nombre_pan = $_POST["nombre_pan"];
        $sql = "DELETE FROM pedidos WHERE Nombre_del_pan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre_pan);
        
        if ($stmt->execute()) {
            echo json_encode(array("success" => true, "message" => "Pedido eliminado exitosamente"));
        } else {
            echo json_encode(array("success" => false, "message" => "Error al eliminar el pedido"));
        }
        exit();
    } elseif (isset($_POST["editar"])) {
        $nombre_pan_original = $_POST["nombre_pan_original"];
        $nombre_pan = $_POST["nombre_pan"];
        $precio = $_POST["precio"];
        $cantidad = $_POST["cantidad"];
        $fecha_elaboracion = $_POST["fecha_elaboracion"];
        $fecha_entrega = $_POST["fecha_entrega"];
        $sucursal = $_POST["sucursal"];

        $sql = "UPDATE pedidos SET Nombre_del_pan = ?, Precio = ?, Cantidad = ?, Fecha_elaboracion = ?, Fecha_entrega = ?, Sucursal = ? WHERE Nombre_del_pan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdissss", $nombre_pan, $precio, $cantidad, $fecha_elaboracion, $fecha_entrega, $sucursal, $nombre_pan_original);

        if ($stmt->execute()) {
            echo json_encode(array("success" => true, "message" => "Pedido actualizado exitosamente"));
        } else {
            echo json_encode(array("success" => false, "message" => "Error al actualizar el pedido"));
        }
        exit();
    }
}

$sql = "SELECT Nombre_del_pan, Precio, Cantidad, Fecha_elaboracion, Fecha_entrega, Sucursal FROM pedidos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Pedidos</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="table/datatables.min.css">
    <style>
        body {
            padding-top: 50px;
            background-image: url('panaderia.gif');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
        }
        h1{
            background-color: #735331;
            border-radius: 20px;
            color: whitesmoke;
        }
        .container {
            max-width: 800px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Pedidos</h1>
        <table id="tabla_1" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Nombre del Pan</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Fecha de Creación</th>
                    <th>Fecha de Entrega</th>
                    <th>Sucursal</th>
                    <th>Precio total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $precio_total = $row["Precio"] * $row["Cantidad"];
                        echo "<tr>";
                        echo "<td class='nombre_pan'>" . $row["Nombre_del_pan"] . "</td>";
                        echo "<td class='precio'>" . $row["Precio"] . "</td>";
                        echo "<td class='cantidad'>" . $row["Cantidad"] . "</td>";
                        echo "<td class='fecha_elaboracion'>" . $row["Fecha_elaboracion"] . "</td>";
                        echo "<td class='fecha_entrega'>" . $row["Fecha_entrega"] . "</td>";
                        echo "<td class='sucursal'>" . $row["Sucursal"] . "</td>";
                        echo "<td class='precio_total'>" . $precio_total . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay pedidos</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <input type="button" class="btn btn-outline-danger" value="Cerrar Sesión" onclick="window.location.href='Cerrar.php'">
    </div>

    <script src="jaqueri/jquery-3.7.1.min.js"></script>
    <script src="bootstrap/bootstrap.min.js"></script>
    <script src="table/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#tabla_1').DataTable({
                "lengthChange": false,  // Desactiva el control de "Entries per page"
                "info":false,
                "lengthMenu":false,
                "paging":false,
                "language": {
                    "emptyTable": "No hay datos disponibles",
                    "infoEmpty": "Mostrando 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros coincidentes",
                    "aria": {
                        "sortAscending": ": activar para ordenar de manera ascendente",
                        "sortDescending": ": activar para ordenar de manera descendente"
                    }
                }
            });
        });
    </script>
</body>
</html>
