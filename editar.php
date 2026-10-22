
<?php
require_once "conexion.php";

if (!isset($_GET["id"])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET["id"];
$mensaje = "";

$consulta = $conexion->prepare(
    "SELECT pedidos.id_pedido, pedidos.id_cliente,
            clientes.nombre, pedidos.producto,
            pedidos.fecha
     FROM pedidos
     INNER JOIN clientes
     ON pedidos.id_cliente = clientes.id_cliente
     WHERE pedidos.id_pedido = ?"
);

$consulta->bind_param("i", $id);
$consulta->execute();
$resultado = $consulta->get_result();
$pedido = $resultado->fetch_assoc();
$consulta->close();

if (!$pedido) {
    die("El pedido no existe.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente = trim($_POST["cliente"]);
    $producto = trim($_POST["producto"]);
    $fecha = $_POST["fecha"];

    if ($cliente != "" && $producto != "" && $fecha != ""
        && $fecha <= date("Y-m-d")) {

        $actualizar = $conexion->prepare(
            "UPDATE clientes SET nombre = ? WHERE id_cliente = ?"
        );
        $actualizar->bind_param(
            "si", $cliente, $pedido["id_cliente"]
        );
        $actualizar->execute();
        $actualizar->close();

        $actualizar = $conexion->prepare(
            "UPDATE pedidos
             SET producto = ?, fecha = ?
             WHERE id_pedido = ?"
        );
        $actualizar->bind_param(
            "ssi", $producto, $fecha, $id
        );

        if ($actualizar->execute()) {
            $actualizar->close();
            header("Location: listar.php");
            exit;
        }

        $actualizar->close();
        $mensaje = "No se pudo modificar el pedido.";
    } else {
        $mensaje = "Completa todos los campos y revisa la fecha.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar pedido</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<h1>Modificar pedido</h1>

<?php if ($mensaje != ""): ?>
    <p><?php echo htmlspecialchars($mensaje); ?></p>
<?php endif; ?>

<form method="POST"
      style="max-width: 400px; margin: 30px auto; background: white; padding: 25px; border-radius: 12px;">

    <label>Nombre del cliente:</label>
    <input type="text" name="cliente" maxlength="100" required
           value="<?php echo htmlspecialchars($pedido["nombre"]); ?>"
           style="width: 100%; padding: 10px; margin: 10px 0 20px; box-sizing: border-box;">

    <label>Producto:</label>
    <input type="text" name="producto" maxlength="100" required
           value="<?php echo htmlspecialchars($pedido["producto"]); ?>"
           style="width: 100%; padding: 10px; margin: 10px 0 20px; box-sizing: border-box;">

    <label>Fecha del pedido:</label>
    <input type="date" name="fecha"
           max="<?php echo date('Y-m-d'); ?>"
           value="<?php echo htmlspecialchars($pedido["fecha"]); ?>"
           required
           style="width: 100%; padding: 10px; margin: 10px 0 20px; box-sizing: border-box;">

    <button type="submit"
            style="background-color: #2563eb; color: white; padding: 12px 20px; border: none; border-radius: 20px; cursor: pointer;">
        Guardar cambios
    </button>

    <a href="listar.php" style="margin-left: 10px;">Cancelar</a>
</form>

</body>
</html>
