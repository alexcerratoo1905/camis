<?php

class Pedido
{
    private $conexionDataBase;
    private $idPedido;
    private $idUsuario;
    private $total;

    public function __construct($db)
    {
        $this->conexionDataBase = $db;
    }

    public function getIdPedido()
    {
        return $this->idPedido;
    }
    public function setIdPedido($idPedido)
    {
        $this->idPedido = $idPedido;
        return $this;
    }
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }
    public function getTotal()
    {
        return $this->total;
    }
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    public function crearPedido($idUsu, $total, $direccionPedido)
    {
        $sql = "INSERT INTO pedidos (usuario_id, total, estado, direccion_envio) 
                VALUES (:usuarioId, :total, 'pendiente', :direccion)";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([
            ":usuarioId" => $idUsu,
            ":total" => $total,
            ":direccion" => $direccionPedido
        ]);

        return $this->conexionDataBase->lastInsertId();
    }

public function crearDetallesPedidos($idPedido, $idPrenda, $idColor, $talla, $cantidad)
    {
        $sqlInfo = "SELECT precio, rebaja FROM productos WHERE id = :idProducto";
        $stmtInfo = $this->conexionDataBase->prepare($sqlInfo);
        $stmtInfo->execute([":idProducto" => $idPrenda]);
        $infoProducto = $stmtInfo->fetch(PDO::FETCH_ASSOC);
        
        $rebaja = isset($infoProducto['rebaja']) ? (int)$infoProducto['rebaja'] : 0;
        $precioUnitario = $infoProducto['precio'] - ($infoProducto['precio'] * $rebaja / 100);

        $sql = "INSERT INTO lineas_pedido (pedido_id, producto_id, color_id, talla, cantidad, precio_unitario) 
                VALUES (:idPedido, :idProducto, :idColor, :talla, :cantidad, :precio)";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([
            ":idPedido"   => $idPedido,
            ":idProducto" => $idPrenda,
            ":idColor"    => $idColor,
            ":talla"      => $talla,
            ":cantidad"   => $cantidad,
            ":precio"     => $precioUnitario
        ]);

        return true;
    }

    public function listarPedidos($idUsu = null)
    {
        $sql = "SELECT pedidos.*, usuarios.nombre AS nombre_cliente 
                FROM pedidos 
                JOIN usuarios ON pedidos.usuario_id = usuarios.id ";

        if ($idUsu !== null) {
            $sql .= "WHERE pedidos.usuario_id = :idUsu ";
        }

        $sql .= "ORDER BY pedidos.fecha DESC";

        $sentencia = $this->conexionDataBase->prepare($sql);

        if ($idUsu !== null) {
            $sentencia->execute([":idUsu" => $idUsu]);
        } else {
            $sentencia->execute(); 
        }

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerInfoPedido($idPedido)
    {
        $sql = "SELECT lp.*, p.nombre AS producto_nombre, c.nombre AS color_nombre, ip.url_imagen 
                FROM lineas_pedido lp
                INNER JOIN productos p ON lp.producto_id = p.id
                LEFT JOIN colores c ON lp.color_id = c.id
                LEFT JOIN imagenes_productos ip ON p.id = ip.producto_id AND ip.color_id = c.id AND ip.es_principal = 1 
                WHERE lp.pedido_id = :idPedido";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":idPedido" => $idPedido]);

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstadoPedido($idPedido, $estado){

    $sql = "UPDATE pedidos SET estado = :estado WHERE id = :idPedido";
    $sentencia = $this->conexionDataBase->prepare($sql);
    $sentencia -> execute([
        ":estado" => $estado,
        ":idPedido" => $idPedido
    ]);

    }
}
