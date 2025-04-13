<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Promise;
use React\MySQL\Factory;

require_once __DIR__ . '/../Config/Database.php';

$db = Database::getInstance()->getConnection();
$method = $request->getMethod();

switch ($method) {
    case 'GET':
        // Obtener todos los productos
        $stmt = $db->query("SELECT * FROM productos");
        $productos = $stmt->fetchAll();
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($productos));

    case 'POST':
        // Crear nuevo producto
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['nombre']) || !isset($data['precio'])) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'Nombre y precio son requeridos']));
        }

        $stmt = $db->prepare("INSERT INTO productos (nombre, descripcion, precio) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['nombre'],
            $data['descripcion'] ?? '',
            $data['precio']
        ]);

        return new Response(201, ['Content-Type' => 'application/json'], json_encode(['id' => $db->lastInsertId()]));

    case 'PUT':
        return new Promise(function ($resolve) use ($db, $request) {
            $body = json_decode((string) $request->getBody(), true);
            $id = $body['id'] ?? null;
            $campo = $body['campo'] ?? '';
            $valor = $body['valor'] ?? '';

            if (!$id || !in_array($campo, ['nombre', 'descripcion', 'precio'])) {
                $resolve(new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'Datos inválidos'])));
                return;
            }

            $query = "UPDATE productos SET $campo = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$valor, $id]);

            $resolve(
                new Response(200, ['Content-Type' => 'application/json'], json_encode(['success' => true]))
            );
        });

    case 'DELETE':
        return new Promise(function ($resolve) use ($db, $request) {
            $body = json_decode((string) $request->getBody(), true);
            $id = $body['id'] ?? null;

            if (!$id) {
                $resolve(new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'ID requerido'])));
                return;
            }

            $stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);

            $resolve(
                new Response(200, ['Content-Type' => 'application/json'], json_encode(['success' => true]))
            );
        });

    default:
        return new Response(405, ['Content-Type' => 'application/json'], json_encode(['error' => 'Método no permitido']));
}