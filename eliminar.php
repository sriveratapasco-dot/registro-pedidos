
<?php
require_once "conexion.php";

if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];

    $consulta = $conexion->prepare(
        "DELETE FROM pedidos WHERE id_pedido = ?"
    );

    $consulta->bind_param("i", $id);
    $consulta->execute();
    $consulta->close();
}

header("Location: listar.php");
exit;
?>
