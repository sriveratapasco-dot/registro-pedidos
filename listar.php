
<?php
require_once "conexion.php";


$busqueda = "";

$sql = "SELECT pedidos.id_pedido, clientes.nombre,
               pedidos.producto, pedidos.fecha, pedidos.estado
        FROM pedidos
        INNER JOIN clientes
        ON pedidos.id_cliente = clientes.id_cliente";

if (isset($_GET["buscar"])) {
    $busqueda = trim($_GET["buscar"]);

    $sql .= " WHERE clientes.nombre LIKE ?
              OR pedidos.producto LIKE ?";

    $consulta = $conexion->prepare(
        $sql . " ORDER BY pedidos.id_pedido DESC"
    );

    $texto = "%" . $busqueda . "%";
    $consulta->bind_param("ss", $texto, $texto);
    $consulta->execute();

    $resultado = $consulta->get_result();
} else {
    $sql .= " ORDER BY pedidos.id_pedido DESC";
    $resultado = $conexion->query($sql);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos registrados</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Pedidos registrados</h1>
    
<form method="GET" action="listar.php"
      style="text-align: center; margin: 20px;">
    <input type="text" name="buscar"
           placeholder="Buscar cliente o producto"
           value="<?php echo htmlspecialchars($busqueda); ?>"
           style="padding: 10px; width: 250px; max-width: 90%;">

    <button type="submit"
            style="background-color: #2563eb; color: white; padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer;">
        Buscar
    </button>

    <a href="listar.php" style="margin-left: 8px;">Mostrar todos</a>
</form>


    <div style="max-width: 1100px; margin: 30px auto; overflow-x: auto;">
        <table style="width: 100%; background: white; border-collapse: collapse;">
            <tr style="background-color: #2563eb; color: white;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Cliente</th>
                <th style="padding: 12px;">Producto</th>
                <th style="padding: 12px;">Fecha</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px;">Acción</th>
                <th style="padding: 12px;">Modificar</th>
                <th style="padding: 12px;">Eliminar</th>
            </tr>

            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td style="padding: 12px; text-align: center;">
                        <?php echo $fila["id_pedido"]; ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo htmlspecialchars($fila["nombre"]); ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo htmlspecialchars($fila["producto"]); ?>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <?php echo $fila["fecha"]; ?>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <?php echo htmlspecialchars($fila["estado"]); ?>
                    </td>
                    <td style="padding: 12px; text-align: center;">
   


<td style="padding: 12px; text-align: center;">
    <a href="cambiar_estado.php?id=<?php echo $fila['id_pedido']; ?>"
       style="background-color: <?php echo $fila['estado'] == 'Pendiente' ? '#16a34a' : '#d97706'; ?>; color: white; padding: 8px 10px; border-radius: 6px; text-decoration: none; display: inline-block; min-width: 130px; box-sizing: border-box;">
        <?php
        if ($fila['estado'] == 'Pendiente') {
            echo 'Marcar entregado';
        } else {
            echo 'Volver a pendiente';
        }
        ?>
    </a>
</td>

       
<td style="padding: 12px; text-align: center;">
    <a href="editar.php?id=<?php echo $fila["id_pedido"]; ?>"
       style="background-color: #2563eb; color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none;">
        Editar
    </a>
</td>

<td style="padding: 12px; text-align: center;">
    <a href="eliminar.php?id=<?php echo $fila["id_pedido"]; ?>"
       onclick="return confirm('¿Estás seguro de que deseas eliminar este pedido?');"
       style="background-color: #dc2626; color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none;">
        Eliminar
    </a>
</td>

                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <p>
        <a href="registrar.php">Registrar otro pedido</a>
        |
        <a href="index.php">Volver al inicio</a>
    </p>

</body>
</html>
