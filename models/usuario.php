<?php

class Usuario
{

    private $conexionDataBase;
    private $idUsuario;
    private $idRol;
    private $nombre;
    private $apellidos;
    private $email;
    private $password;
    private $telefono;
    private $direccion;
    private $ciudad;
    private $codigoPostal;
    private $puntosFidelidad;
    private $fechaRegistro;

    public function __construct($db)
    {
        $this->conexionDataBase = $db;
    }

    /**
     * Get the value of idUsuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set the value of idUsuario
     *
     * @return  self
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get the value of idRol
     */
    public function getIdRol()
    {
        return $this->idRol;
    }

    /**
     * Set the value of idRol
     *
     * @return  self
     */
    public function setIdRol($idRol)
    {
        $this->idRol = $idRol;

        return $this;
    }

    /**
     * Get the value of nombre
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of apellidos
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set the value of apellidos
     *
     * @return  self
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of telefono
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set the value of telefono
     *
     * @return  self
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get the value of direccion
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set the value of direccion
     *
     * @return  self
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get the value of ciudad
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set the value of ciudad
     *
     * @return  self
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get the value of codigoPostal
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * Set the value of codigoPostal
     *
     * @return  self
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get the value of puntosFidelidad
     */
    public function getPuntosFidelidad()
    {
        return $this->puntosFidelidad;
    }

    /**
     * Set the value of puntosFidelidad
     *
     * @return  self
     */
    public function setPuntosFidelidad($puntosFidelidad)
    {
        $this->puntosFidelidad = $puntosFidelidad;

        return $this;
    }

    /**
     * Get the value of fechaRegistro
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set the value of fechaRegistro
     *
     * @return  self
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    public function registrar()
    {
        try {
            $sql = "INSERT INTO usuarios(nombre, apellidos, email, password, rol_id) values (:nombre, :apellidos, :email, :password, :idRol)";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":nombre" => $this->nombre,
                ":apellidos" => $this->apellidos,
                ":email" => $this->email,
                ":password" => password_hash($this->password, PASSWORD_DEFAULT),
                ":idRol" => $this->idRol
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password)
    {
        try {
            $sql = "SELECT * from usuarios where email = :email";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([":email" => $email]);

            $datosUsu = $sentencia->fetch(PDO::FETCH_ASSOC);

            if ($datosUsu && password_verify($password, $datosUsu["password"])) {
                return $datosUsu;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDatosUsu($idUsu)
    {
        $sql = "SELECT * from usuarios where id = :idUsu";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":idUsu" => $idUsu]);

        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarDatosUsu()
    {
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, telefono = :telefono, direccion = :direccion, ciudad = :ciudad, codigo_postal = :codigoPostal WHERE id = :idUsu";

            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":nombre" => $this->nombre,
                ":apellidos" => $this->apellidos,
                ":telefono" => $this->telefono,
                ":direccion" => $this->direccion,
                ":ciudad" => $this->ciudad,
                ":codigoPostal" => $this->codigoPostal,
                ":idUsu" => $this->idUsuario
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar: " . $e->getMessage());
            return false;
        }
    }

    public function cambiarPass($passNueva, $idUsu)
    {
        try {
            $sql = "UPDATE usuarios SET password = :passNueva WHERE id = :idUsu";

            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":passNueva" => $passNueva,
                ":idUsu" => $idUsu
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar: " . $e->getMessage());
            return false;
        }
    }

    public function buscarUsuPorCorreo($emailABuscar)
    {
        try {
            $sql = "SELECT * from usuarios where email = :emailABuscar";

            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([":emailABuscar" => $emailABuscar]);

            if ($sentencia->fetchALL(PDO::FETCH_ASSOC)) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar: " . $e->getMessage());
            return false;
        }
    }

    public function anadirTokenRecuperarPass($emailCambiarPass)
    {
        $token = bin2hex(random_bytes(32));

        $fechaCaducidad = date('Y-m-d H:i:s', strtotime('+1 hour'));

        try {
            $sql = "UPDATE usuarios SET token_cambiar_password = :token, caducidad_token = :caducidad where email = :emailCambiarPass";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":token" => $token,
                ":caducidad" => $fechaCaducidad,
                ":emailCambiarPass" => $emailCambiarPass
            ]);

            return $token;
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPasswordPorToken($token, $nuevaPasswordHash)
    {
        try {
            $sql = "SELECT id, caducidad_token FROM usuarios WHERE token_cambiar_password = :token";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([':token' => $token]);

            if ($sentencia->rowCount() > 0) {
                $fila = $sentencia->fetch(PDO::FETCH_ASSOC);
                $idUsu = $fila['id'];
                $caducidad = $fila['caducidad_token'];

                date_default_timezone_set('Europe/Madrid');

                if (strtotime(date("Y-m-d H:i:s")) > strtotime($caducidad)) {
                    return "caducado";
                }

                $sqlUpd = "UPDATE usuarios SET password = :password, token_cambiar_password = NULL, caducidad_token = NULL WHERE id = :idUsu";
                $sentenciaUpd = $this->conexionDataBase->prepare($sqlUpd);
                $exito = $sentenciaUpd->execute([
                    ':password' => $nuevaPasswordHash,
                    ':idUsu' => $idUsu
                ]);

                if ($exito) {
                    return "exito";
                }
            }
            return "invalido";
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar: " . $e->getMessage());
            return "invalido";
        }
    }

    public function comprobarDescuento($email)
    {
        $sql = "SELECT * from codigos_accesos where email = :email AND porcentaje_descuento = 10";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":email" => $email]);
        if ($sentencia->fetch()) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    public function generarCodigoDescuento(){
        return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }


    public function crearCodigo($codigoDescuento, $email,  $tipo = "acceso")
    {
        $sql = "INSERT INTO codigos_accesos (codigo, email, tipo) VALUES (:codigo, :email, :tipo)";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([
            ":codigo" => $codigoDescuento,
            ":email" => $email,
            ":tipo" => $tipo
        ]);

    }

    public function listarUsuarios() {
        $sql = "SELECT u.id, u.nombre, u.email, u.rol_id, r.nombre_rol as nombre_rol 
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                ORDER BY u.id DESC";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarRolUsuario($idUsuario, $idRol) {
        $sql = "UPDATE usuarios SET rol_id = :rol_id WHERE id = :id";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([':rol_id' => $idRol, ':id' => $idUsuario]);
    }

    public function marcarCodigoUsado($codigo) {
    try {
        $sql = "UPDATE codigos_accesos SET usado = 1 WHERE codigo = :codigo AND usado = 0";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([':codigo' => $codigo]);
    } catch (PDOException $e) {
        error_log("Error al marcar código como usado: " . $e->getMessage());
        return false;
    }
}


}
