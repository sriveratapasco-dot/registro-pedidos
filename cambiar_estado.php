
<?php
require_once "conexion.php";

if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];

    $consulta = $conexion->prepare(
        "SELECT estado FROM pedidos WHERE id_pedido = ?"
    );
    $consulta->bind_param("i", $id);
    $consulta->execute();
    $consulta->bind_result($estado_actual);

    if ($consulta->fetch()) {
        $consulta->close();

        if ($estado_actual == "Pendiente") {
            $nuevo_estado = "Entregado";
        } else {
            $nuevo_estado = "Pendiente";
        }

        $actualizar = $conexion->prepare(
            "UPDATE pedidos SET estado = ? WHERE id_pedido = ?"
        );
        $actualizar->bind_param("si", $nuevo_estado, $id);
        $actualizar->execute();
        $actualizar->close();
    } else {
        $consulta->close();
    }
}

header("Location: listar.php");
exit;
?>


