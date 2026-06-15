<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Protección de rango
redirigirSiNoLogueado("../index.php");
restringirAccesoA($_SESSION["rol_id"], [1, 3], "../index.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $idPedido = (int)$_POST['id_pedido'];
    $emailCliente = filter_var($_POST['email_cliente'], FILTER_SANITIZE_EMAIL);
    $nombreCliente = htmlspecialchars($_POST['nombre_cliente']);
    $trackingNumber = htmlspecialchars($_POST['tracking_number']);
    $trackingUrl = filter_var($_POST['tracking_url'], FILTER_VALIDATE_URL);

    if (empty($trackingNumber) || empty($trackingUrl)) {
        die("Error: Faltan el número o el enlace de seguimiento.");
    }

    $db = new Database();
    $conexion = $db->conectar();

    try {
        // HTML del correo
        $mensajeHTML = "
        <html>
        <body style='font-family: Arial, sans-serif; color: #111; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: #000; color: #fff; padding: 20px; text-align: center;'>
                <h1 style='margin: 0; text-transform: uppercase; letter-spacing: 2px;'>HERROR</h1>
            </div>
            <div style='padding: 20px; border: 1px solid #ddd; border-top: none;'>
                <p>¡Hola, <strong>$nombreCliente</strong>!</p>
                <p>Buenas noticias: tu paquete ya ha sido procesado por el proveedor y está de camino a tu dirección registrada.</p>
                
                <div style='background: #f9f9f9; border: 1px dashed #000; padding: 15px; margin: 20px 0; text-align: center;'>
                    <p style='margin: 0 0 10px 0; text-transform: uppercase; font-size: 12px; font-weight: bold; color: #666;'>Tu Código de Seguimiento:</p>
                    <span style='font-size: 18px; font-weight: bold; font-family: monospace; letter-spacing: 1px;'>$trackingNumber</span>
                </div>

                <p style='text-align: center; margin-top: 30px;'>
                    <a href='$trackingUrl' target='_blank' style='background: #000; color: #fff; text-decoration: none; padding: 12px 25px; font-weight: bold; text-transform: uppercase; display: inline-block;'>Localizar Mi Envío</a>
                </p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                <p style='font-size: 12px; color: #777;'>El plazo estimado de entrega es de 10-12 días laborables.</p>
            </div>
        </body>
        </html>
        ";

        // ==========================================
        // ENVÍO GRATUITO POR API DE BREVO (Sortea DigitalOcean)
        // ==========================================
        $api_key = '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';
        $correo_remitente = 'aleexcm19@gmail.com';

        $datosBrevo = [
            "sender" => [
                "name" => "HERROR Tienda",
                "email" => $correo_remitente
            ],
            "to" => [
                [
                    "email" => $emailCliente,
                    "name" => $nombreCliente
                ]
            ],
            "subject" => "¡Tu pedido de HERROR ha sido enviado! (Pedido #$idPedido)",
            "htmlContent" => $mensajeHTML
        ];

        // Conexión segura cURL (Puerto 443 web normal)
        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosBrevo));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'api-key: ' . $api_key,
            'content-type: application/json'
        ]);

        $respuesta = curl_exec($ch);
        $codigoHTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Validamos si la API procesó el envío gratis (Devuelve 201)
        if ($codigoHTTP !== 201) {
            die("<div style='padding:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;'>
                    <h3>Error con la API Gratuita</h3>
                    <p>Código: $codigoHTTP</p>
                    <p>Respuesta: $respuesta</p>
                 </div>");
        }

        // Éxito total. Actualizamos la Base de Datos a 'Enviado'
        $conexion->beginTransaction();
        $stmt = $conexion->prepare("UPDATE pedidos SET estado = 'enviado' WHERE id = ?");
        $stmt->execute([$idPedido]);
        $conexion->commit();
        
        header("Location: ../admin/admin.php?seccion=pedidos&mensaje=tracking_enviado");
        exit();

    } catch (Exception $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        die("Error crítico interno: " . $e->getMessage());
    }

} else {
    header("Location: ../admin/admin.php?seccion=pedidos");
    exit();
}