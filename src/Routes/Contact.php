<?php

use React\Http\Message\Response;

function handleContactRequest($request, $method) {
    $db = new PDO('mysql:host=localhost;dbname=reactphp_event_server', 'root', '');

    switch ($method) {
        case 'POST':
            $data = json_decode($request->getBody(), true);
            
            if (!isset($data['nombre']) || !isset($data['correo']) || !isset($data['asunto']) || !isset($data['mensaje'])) {
                return new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'Faltan campos requeridos']));
            }

            $stmt = $db->prepare('INSERT INTO contacto (nombre, correo, asunto, mensaje) VALUES (?, ?, ?, ?)');
            $stmt->execute([$data['nombre'], $data['correo'], $data['asunto'], $data['mensaje']]);

            return new Response(201, ['Content-Type' => 'application/json'], json_encode(['message' => 'Mensaje enviado correctamente']));

        case 'GET':
            $stmt = $db->query('SELECT * FROM contacto ORDER BY id DESC');
            $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return new Response(200, ['Content-Type' => 'application/json'], json_encode($contactos));

        default:
            return new Response(405, ['Content-Type' => 'application/json'], json_encode(['error' => 'Método no permitido']));
    }
}

return handleContactRequest($request, $method); 