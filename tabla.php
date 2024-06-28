<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "Tsuki07$-";
$dbname = "distribucion_de_panes1";
$port = 3306;

// Si el usuario es distinto de Johnny o Alexis, redirigir a vista_usuario.php
if ($_SESSION['username'] != 'Johnny' && $_SESSION['username'] != 'Alexis') {
    header("Location: vista_usuario.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre_pan"])) {
    $nombre_pan = $conn->real_escape_string($_POST["nombre_pan"]);

    if (isset($_POST["eliminar"])) {
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
        $nombre_pan_original = $conn->real_escape_string($_POST["nombre_pan_original"]);
        $nombre_pan = $conn->real_escape_string($_POST["nombre_pan"]);
        $cantidad = intval($_POST["cantidad"]);
        $fecha_elaboracion = $conn->real_escape_string($_POST["fecha_elaboracion"]);
        $fecha_entrega = $conn->real_escape_string($_POST["fecha_entrega"]);
        $sucursal = preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/u', '', $_POST["sucursal"]);

        if ($cantidad < 1 || $cantidad > 100) {
            echo json_encode(array("success" => false, "message" => "La cantidad debe estar entre 1 y 100"));
            exit();
        }

        $sql_precio = "SELECT Precio FROM pedidos WHERE Nombre_del_pan = ?";
        $stmt_precio = $conn->prepare($sql_precio);
        $stmt_precio->bind_param("s", $nombre_pan_original);
        $stmt_precio->execute();
        $result_precio = $stmt_precio->get_result();
        if ($result_precio->num_rows > 0) {
            $row_precio = $result_precio->fetch_assoc();
            $precio = $row_precio["Precio"];
        } else {
            echo json_encode(array("success" => false, "message" => "No se encontró el precio del pan"));
            exit();
        }

        $sql = "UPDATE pedidos SET Nombre_del_pan = ?, Precio = ?, Cantidad = ?, Fecha_elaboracion = ?, Fecha_entrega = ?, Sucursal = ? WHERE Nombre_del_pan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidssss", $nombre_pan, $precio, $cantidad, $fecha_elaboracion, $fecha_entrega, $sucursal, $nombre_pan_original);

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

$tipos_de_pan = [
    "Concha" => 15,
    "Oreja" => 12,
    "Besos" => 20,
    "Trenza" => 10,
    "Rejilla" => 10,
    "Bisquet" => 25,
    "Cuernito" => 12,
    "Dona de chocolate" => 20,
    "Dona de moca" => 25,
    "Ladrillo" => 9,
    "Pan de elote" => 26,
    "Pan de nata" => 25,
    "Pan de mil hojas" => 27,
    "Empanadas" => 21,
    "Cocoles" => 18
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Pedidos</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="table/datatables.min.css">
    <style>
        body {
            padding-top: 50px;
            background-image: url('naranja.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            background-size: 100% 100%;
            margin: 0;
        }
        .container {
            max-width: 900px;
        }
        h1{
            background-color: #621F2C;
            border-radius: 20px;
            color: whitesmoke;
        }
        .swal2-input{
            border-radius: 20px;
            background-color: #DFC5CA;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Registro de Pedidos</h1>
        <table id="tabla_1" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Nombre del Pan</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Fecha de Pedido</th>
                    <th>Fecha de Entrega</th>
                    <th>Sucursal</th>
                    <th>Precio Total</th>
                    <th>Acciones</th>
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
                        echo "<td>";
                        echo "<button type='button' class='btn btn-danger' onclick='eliminarFila(\"" . $row["Nombre_del_pan"] . "\")'>Eliminar</button>";
                        echo "<button type='button' class='btn btn-secondary' onclick='editarFila(this)'>Editar</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay pedidos</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <a href="inicio.php" class="btn btn-primary">Volver</a>
    </div>

    <script src="jaqueri/jquery-3.7.1.min.js"></script>
    <script src="bootstrap/bootstrap.min.js"></script>
    <script src="table/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#tabla_1').DataTable();
        });

        function eliminarFila(nombre) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir los cambios!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "¡Sí, bórralo!",
                cancelButtonText: "No, Cancelar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "tabla.php",
                        data: { eliminar: true, nombre_pan: nombre },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.success) {
                                Swal.fire({
                                    title: "¡Ha sido eliminado!",
                                    text: "Tu archivo ha sido eliminado.",
                                    icon: "success"
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: data.message,
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr, status, errorThrown) {
                            Swal.fire({
                                title: "Error!",
                                text: "Error al eliminar el pedido",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }

        function editarFila(button) {
            var row = $(button).closest('tr');
            var nombre_pan = row.find('.nombre_pan').text();
            var precio = parseFloat(row.find('.precio').text());
            var cantidad = parseInt(row.find('.cantidad').text());
            var fecha_elaboracion = row.find('.fecha_elaboracion').text();
            var fecha_entrega = row.find('.fecha_entrega').text();
            var sucursal = row.find('.sucursal').text();

            var tipos_de_pan = <?php echo json_encode($tipos_de_pan); ?>;
            var select_html = '<select id="nombre_pan" class="swal2-input">';
            for (var tipo in tipos_de_pan) {
                var selected = tipo === nombre_pan ? 'selected' : '';
                select_html += '<option value="' + tipo + '" data-precio="' + tipos_de_pan[tipo] + '" ' + selected + '>' + tipo + '</option>';
            }
            select_html += '</select>';

            function abrirSweetAlertEditar() {
                Swal.fire({
                    title: 'Editar Pedido',
                    html: `
                        <input type="hidden" id="nombre_pan_original" value="${nombre_pan}"><br>
                        <label>Nombre del Pan:</label>
                        ${select_html}<br>
                        <label>Precio</label><br>
                        <input type="number" id="precio" class="swal2-input" value="${precio}" readonly><br>
                        <label>Cantidad</label><br>
                        <input type="number" id="cantidad" class="swal2-input" value="${cantidad}" min="1" max="100" required><br>
                        <label>Fecha de Pedido</label><br>
                        <input type="text" id="fecha_elaboracion" class="swal2-input" value="${fecha_elaboracion}" readonly><br>
                        <label>Fecha de Entrega</label><br>
                        <input type="date" id="fecha_entrega" class="swal2-input" value="${fecha_entrega}" min="${fecha_elaboracion}" max="${getMaxFechaEntrega(fecha_elaboracion)}"><br>
                        <label>Sucursal</label><br>
                        <input type="text" id="sucursal" class="swal2-input" value="${sucursal}" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo se permiten letras y acentos">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Guardar Cambios',
                    preConfirm: () => {
                        return new Promise((resolve, reject) => {
                            var cantidad = $('#cantidad').val();
                            if (cantidad < 1 || cantidad > 100) {
                                reject("La cantidad debe estar entre 1 y 100");
                                return;
                            }

                            $.ajax({
                                type: 'POST',
                                url: 'tabla.php',
                                data: {
                                    editar: true,
                                    nombre_pan_original: $('#nombre_pan_original').val(),
                                    nombre_pan: $('#nombre_pan').val(),
                                    cantidad: cantidad,
                                    fecha_elaboracion: $('#fecha_elaboracion').val(),
                                    fecha_entrega: $('#fecha_entrega').val(),
                                    sucursal: $('#sucursal').val()
                                },
                                success: function(response) {
                                    var data = JSON.parse(response);
                                    if (data.success) {
                                        resolve(data);
                                    } else {
                                        reject(data.message);
                                    }
                                },
                                error: function(xhr, status, errorThrown) {
                                    reject("Error al modificar el pedido");
                                }
                            });
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire("¡Actualizado!", "El pedido ha sido actualizado.", "success")
                        .then(() => location.reload());
                    }
                }).catch((error) => {
                    Swal.fire("¡Error!", error, "volver a llenar los campos.").then(() => abrirSweetAlertEditar());
                });

                $('#nombre_pan').change(function() {
                    var selectedOption = $(this).find('option:selected');
                    var precioNuevo = selectedOption.data('precio');
                    $('#precio').val(precioNuevo);
                });
            }

            abrirSweetAlertEditar();
        }

        function getMaxFechaEntrega(fecha_elaboracion) {
            var fecha = new Date(fecha_elaboracion);
            fecha.setDate(fecha.getDate() + 3);
            return fecha.toISOString().split('T')[0];
        }
    </script>
</body>
</html>
