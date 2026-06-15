<?php
class Cita {
    private $conexionDataBase;

    public function __construct($db) {
        $this->conexionDataBase = $db;
    }

    public function obtenerCitasUsuario($idUsu) {
        $sql = "SELECT * FROM citas WHERE usuario_id = :idUsu ORDER BY fecha_cita DESC";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute(["idUsu" => $idUsu]);
        
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

public function buscarHorasLibresChatBot($fecha, $hora = null) {
        $aforoMaximo = 10;
        $horasLaborales = ['10:00', '11:00', '12:00', '13:00', '17:00', '18:00', '19:00']; 

        if ($hora !== null && $hora !== "") {
            $fechaHoraExacta = $fecha . ' ' . $hora . ':00'; 
            
            $sql = "SELECT COUNT(*) as total_reservas 
                    FROM citas 
                    WHERE fecha_cita = :fechaHora 
                    AND estado != 'cancelada'";
            
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([':fechaHora' => $fechaHoraExacta]);
            $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total_reservas'] < $aforoMaximo) {
                return [['hora' => $hora]]; 
            } else {
                return []; 
            }

        } else {
            $sql = "SELECT DATE_FORMAT(fecha_cita, '%H:%i') as hora_ocupada, COUNT(*) as total_reservas
                    FROM citas
                    WHERE DATE(fecha_cita) = :fecha 
                    AND estado != 'cancelada'
                    GROUP BY hora_ocupada";
            
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([':fecha' => $fecha]);
            $citasOcupadas = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            
            $horasLibres = [];
            
            foreach ($horasLaborales as $horaPosible) {
                $llena = false;
                foreach ($citasOcupadas as $ocupada) {
                    if ($ocupada['hora_ocupada'] === $horaPosible && $ocupada['total_reservas'] >= $aforoMaximo) {
                        $llena = true;
                        break;
                    }
                }
                if (!$llena) {
                    $horasLibres[] = ['hora' => $horaPosible];
                }
            }
            
            return $horasLibres;
        }
    }

}
?>