<?php
session_start();

// Verificar si la sesión está activa y el usuario es válido
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Si no hay sesión activa, redirigir al inicio de sesión
    exit();
}

// Si el usuario es distinto de Johnny o Alexis, redirigir a vista_usuario.php
if ($_SESSION['username'] != 'Johnny' && $_SESSION['username'] != 'Alexis') {
    header("Location: vista_usuario.php");
    exit();
}

// Conexión a la base de datos
$db_host = 'localhost';
$db_username = 'root';
$db_password = 'Tsuki07$-';
$db_name = 'distribucion_de_panes1';

// Crear conexión
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de Pan</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    <style>
        body {
            padding-top: 50px;
            background-image: url('e.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
        }
        .container {
            max-width: 500px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Pedido de Pan</h1>
        <form id="formulario-pedido-pan" action="mequieromatar.php" method="post">
            <div class="mb-3">
                <label for="nombre_pan" class="form-label">Nombre del Pan</label>
                <select class="form-select form-select-lg mb-3" id="nombre_pan" name="nombre_pan" required onchange="actualizarPrecio()">
                    <option value="" selected disabled>Selecciona el pan</option>
                    <option value="Concha">Concha</option>
                    <option value="Oreja">Oreja</option>
                    <option value="Besos">Besos</option>
                    <option value="Trenza">Trenza</option>
                    <option value="Rejilla">Rejilla</option>
                    <option value="Bisquet">Bisquet</option>
                    <option value="Cuernito">Cuernito</option>
                    <option value="Dona de chocolate">Dona de chocolate</option>
                    <option value="Dona de moca">Dona de moca</option>
                    <option value="Ladrillo">Ladrillo</option>
                    <option value="Pan de elote">Pan de elote</option>
                    <option value="Pan de nata">Pan de nata</option>
                    <option value="Pan de mil hojas">Pan de mil hojas</option>
                    <option value="Empanadas">Empanadas</option>
                    <option value="Cocoles">Cocoles</option>
                </select>
                <div id="error-nombre_pan" style="display: none; color: red;">Selecciona el tipo de pan</div>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" readonly>
            </div>
            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="100" required>
                <div id="error-cantidad" style="display: none; color: red;">La cantidad es obligatoria y no puede ser mayor a 100</div>
            </div>
            <div class="mb-3">
                <label for="fecha_creacion" class="form-label">Fecha de pedido</label>
                <input type="date" class="form-control" id="fecha_creacion" name="fecha_creacion" required>
                <div id="error-fecha_creacion" style="display: none; color: red;">Selecciona la fecha de pedido</div>
            </div>
            <div class="mb-3">
                <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
                <div id="error-fecha_entrega" style="display: none; color: red;">Selecciona la fecha de entrega</div>
            </div>
            <div class="mb-3">
                <label for="sucursal" class="form-label">Sucursal para Entregar</label>
                <input type="text" class="form-control" id="sucursal" name="sucursal" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+">
                <div id="error-sucursal" style="display: none; color: red;">Escribe la sucursal de entrega</div>
            </div>
            <div class="btn-container">
                <input type="button" class="btn btn-outline-danger" value="Cerrar Sesión" onclick="window.location.href='Cerrar.php'">
                <button type="submit" class="btn btn-primary">Enviar Pedido</button>
                <button type="button" class="btn btn-success" onclick="window.location.href='tabla.php'">Historial</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="jquery/jquery-3.7.1.min.js"></script>
    <script src="bootstrap/bootstrap.min.js"></script>
    <script>
        const precios = {
            "Concha": 15,
            "Oreja": 18,
            "Besos": 20,
            "Trenza": 25,
            "Rejilla": 17,
            "Bisquet": 22,
            "Cuernito": 18,
            "Dona de chocolate": 15,
            "Dona de moca": 15,
            "Ladrillo": 22,
            "Pan de elote": 20,
            "Pan de nata": 25,
            "Pan de mil hojas": 30,
            "Empanadas": 20,
            "Cocoles": 18
        };

        function actualizarPrecio() {
            const nombrePan = document.getElementById("nombre_pan").value;
            const precio = precios[nombrePan] || 0;
            document.getElementById("precio").value = precio;
        }

        function mostrarError(elemento, mensaje) {
            const errorDiv = document.getElementById("error-" + elemento.id);
            errorDiv.textContent = mensaje;
            errorDiv.style.display = "block";
        }

        function ocultarError(elemento) {
            const errorDiv = document.getElementById("error-" + elemento.id);
            errorDiv.style.display = "none";
        }

        document.getElementById('formulario-pedido-pan').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevenir el envío automático

            const nombrePan = document.getElementById('nombre_pan');
            const cantidad = document.getElementById('cantidad');
            const fechaCreacion = document.getElementById('fecha_creacion');
            const fechaEntrega = document.getElementById('fecha_entrega');
            const sucursal = document.getElementById('sucursal');

            let validacionCorrecta = true;

            if (!nombrePan.value) {
                mostrarError(nombrePan, "Selecciona el tipo de pan");
                validacionCorrecta = false;
            } else {
                ocultarError(nombrePan);
            }

            if (!cantidad.value || parseInt(cantidad.value, 10) > 100) {
                if (!cantidad.value) {
                    mostrarError(cantidad, "La cantidad es obligatoria");
                } else {
                    mostrarError(cantidad, "La cantidad no puede ser mayor a 100");
                }
                validacionCorrecta = false;
            } else {
                ocultarError(cantidad);
            }

            if (!fechaCreacion.value) {
                mostrarError(fechaCreacion, "Selecciona la fecha de creación");
                validacionCorrecta = false;
            } else {
                ocultarError(fechaCreacion);
            }

            if (!fechaEntrega.value) {
                mostrarError(fechaEntrega, "Selecciona la fecha de entrega");
                validacionCorrecta = false;
            } else {
                ocultarError(fechaEntrega);
            }

            const sucursalPattern = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
            if (!sucursal.value) {
                mostrarError(sucursal, "Escribe la sucursal de entrega");
                validacionCorrecta = false;
            } else if (!sucursalPattern.test(sucursal.value)) {
                mostrarError(sucursal, "La sucursal solo puede contener letras y espacios");
                validacionCorrecta = false;
            } else {
                ocultarError(sucursal);
            }

            if (validacionCorrecta) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Pedido registrado correctamente!',
                    showConfirmButton: false,
                    timer: 2000
                });

                setTimeout(function() {
                    document.getElementById("formulario-pedido-pan").submit();
                }, 2000);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const fechaCreacionInput = document.getElementById('fecha_creacion');
            const fechaEntregaInput = document.getElementById('fecha_entrega');

            const hoy = new Date().toISOString().split('T')[0];
            
            // Establecer la fecha de hoy como valor y el único posible en el campo de fecha de creación
            fechaCreacionInput.setAttribute('value', hoy);
            fechaCreacionInput.setAttribute('min', hoy);
            fechaCreacionInput.setAttribute('max', hoy);
            fechaCreacionInput.readOnly = true; // Deshabilitar el campo para que no sea editable

            // Establecer opciones de fecha de entrega en tiempo real
            const fechaCreacion = new Date(hoy);
            const dosDiasDespues = new Date(new Date(fechaCreacion).setDate(fechaCreacion.getDate() + 2)).toISOString().split('T')[0];
            
            fechaEntregaInput.setAttribute('min', hoy);
            fechaEntregaInput.setAttribute('max', dosDiasDespues);

            // Habilitar solo las fechas de hoy y dos días después
            fechaEntregaInput.addEventListener('focus', function() {
                this.value = hoy; // Establecer la fecha de hoy por defecto
                const opcionesFecha = [hoy, dosDiasDespues];
                this.innerHTML = opcionesFecha.map(fecha => `<option value="${fecha}">${fecha}</option>`).join('');
            });
        });
    </script>
</body>
</html>

