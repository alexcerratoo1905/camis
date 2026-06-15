<?php

class Favorito
{
    private $conexionDataBase;

    public function __construct($db)
    {
        $this->conexionDataBase = $db;
    }

    public function esFavorito($idUsu, $idPrenda, $colorPrenda)
    {
        try {
            $sql = "SELECT * FROM favoritos WHERE usuario_id = :idUsu AND producto_id = :idPrenda AND color_id = :colorPrenda";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":idUsu" => $idUsu,
                ":idPrenda" => $idPrenda,
                ":colorPrenda" => $colorPrenda
            ]);

            $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
            return (bool)$resultado;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function agregarFavorito($idUsu, $idPrenda, $colorPrenda)
    {
        try {
            $sql = "INSERT INTO favoritos (usuario_id, producto_id, color_id) VALUES (:idUsu, :idPrenda, :colorPrenda)";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":idUsu" => $idUsu,
                ":idPrenda" => $idPrenda,
                ":colorPrenda" => $colorPrenda
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminarFavoritos($idUsu, $idPrenda, $colorPrenda){
        try {
            $sql = "DELETE FROM favoritos WHERE usuario_id = :idUsu AND producto_id = :idPrenda AND color_id = :colorPrenda";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":idUsu" => $idUsu,
                ":idPrenda" => $idPrenda,
                ":colorPrenda" => $colorPrenda
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function listarFavoritos($idUsu){
        try {
            $sql = "SELECT p.id, p.nombre, p.precio, c.id AS color_id, c.nombre AS color_nombre, MIN(i.url_imagen) AS url_imagen
                    FROM favoritos f
                    INNER JOIN productos p ON f.producto_id = p.id
                    INNER JOIN colores c ON f.color_id = c.id
                    LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.color_id = c.id AND i.es_principal = 1
                    WHERE f.usuario_id = :idUsu
                    GROUP BY p.id, c.id";

            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([":idUsu" => $idUsu]);
            
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>