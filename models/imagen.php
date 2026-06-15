<?php

class Imagen{

    private $idImagen;
    private $idProducto;
    private $urlImagen;
    private $esPrincipal;
    private $conexionDataBase;

    public function __construct($db) {
        $this->conexionDataBase = $db;
    }

    public function getIdImagen() { return $this->idImagen; }
    public function setIdImagen($idImagen) { $this->idImagen = $idImagen; return $this; }
    public function getIdProducto() { return $this->idProducto; }
    public function setIdProducto($idProducto) { $this->idProducto = $idProducto; return $this; }
    public function getUrlImagen() { return $this->urlImagen; }
    public function setUrlImagen($urlImagen) { $this->urlImagen = $urlImagen; return $this; }
    public function getEsPrincipal() { return $this->esPrincipal; }
    public function setEsPrincipal($esPrincipal) { $this->esPrincipal = $esPrincipal; return $this; }

    public function listarImagenes($idPrenda)
    {
        try {
            $sql = "SELECT url_imagen, color_id FROM imagenes_productos WHERE producto_id = :idProducto ORDER BY es_principal DESC";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([":idProducto" => $idPrenda]);
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $sql = "SELECT url_imagen FROM imagenes_productos WHERE producto_id = :idProducto ORDER BY es_principal DESC";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([":idProducto" => $idPrenda]);
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function listarImagenesPorColor($idPrenda, $idColor)
    {
        try {
            $sql = "SELECT url_imagen, color_id FROM imagenes_productos WHERE producto_id = :idProducto AND color_id = :idColor ORDER BY es_principal DESC";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":idProducto" => $idPrenda,
                ":idColor" => $idColor
            ]);
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>