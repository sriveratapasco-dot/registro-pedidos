
<?php
require_once "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente = trim($_POST["cliente"]);
    $producto = trim($_POST["producto"]);
    $fecha = $_POST["fecha"];

    
if (
    $cliente != "" &&
    $producto != "" &&
    $fecha != "" &&
    $fecha <= date("Y-m-d")
) {


        $consulta = $conexion->prepare(
            "INSERT INTO clientes (nombre) VALUES (?)"
        );
        $consulta->bind_param("s", $cliente);

        if ($consulta->execute()) {
            $id_cliente = $conexion->insert_id;

            $consulta_pedido = $conexion->prepare(
                "INSERT INTO pedidos (id_cliente, producto, fecha)
                 VALUES (?, ?, ?)"
            );
            $consulta_pedido->bind_param(
                "iss",
                $id_cliente,
                $producto,
                $fecha
            );

            if ($consulta_pedido->execute()) {
                $mensaje = "¡Pedido registrado correctamente!";
            } else {
                $mensaje = "Error al guardar el pedido.";
            }

            $consulta_pedido->close();
        } else {
            $mensaje = "Error al guardar el cliente.";
        }

        $consulta->close();
    
} else {
    if ($cliente == "" || $producto == "" || $fecha == "") {
        $mensaje = "Completa todos los campos.";
    } else {
        $mensaje = "La fecha no puede ser futura.";
    }
}

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar pedido</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Registrar pedido</h1>

    <?php if ($mensaje != ""): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <form method="POST" style="max-width: 400px; margin: 30px auto; background: white; padding: 25px; border-radius: 12px;">

        <label>Nombre del cliente:</label>
        <input type="text" name="cliente" maxlength="100" required
               style="width: 100%; padding: 10px; margin: 10px 0 20px; box-sizing: border-box;">

        <label>Producto:</label>
        <input type="text" name="producto" maxlength="100" required
               style="width: 100%; padding: 10px; margin: 10px 0 20px; box-sizing: border-box;">

        <label>Fecha del pedido:</label>
        <input type="date" name="fecha"
               max="<?php echo date('Y-m-d'); ?>" required
               style="width: 100%; padding: 10px; margin: 10px 0 20px; box-sizing: border-box;">

        <button type="submit"
                style="background-color: #2563eb; color: white; padding: 12px 20px; border: none; border-radius: 20px; cursor: pointer;">
            Guardar pedido
        </button>

        <a href="index.php" style="margin-left: 10px; color: #2563eb;">Volver</a>

    </form>

</body>
</html>
