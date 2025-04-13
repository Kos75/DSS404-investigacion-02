<?php
require 'vendor/autoload.php';

use React\Http\HttpServer;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\EventLoop\Loop;
use React\Socket\SocketServer;

$loop = Loop::get();

$server = new HttpServer(function (ServerRequestInterface $request) {
    $path = $request->getUri()->getPath();
    $method = $request->getMethod();

    if (strpos($path, '/data') === 0 && $path !== '/data.html') {
        return include __DIR__ . '/src/Routes/Data.php';
    }

    switch ($path) {
        case '/':
            return new Response(200, ['Content-Type' => 'text/html'], file_get_contents(__DIR__ . '/public/index.html'));
        case '/contact':
            return new Response(200, ['Content-Type' => 'text/html'], file_get_contents(__DIR__ . '/public/contact.html'));
        case '/data.html':
            return new Response(200, ['Content-Type' => 'text/html'], file_get_contents(__DIR__ . '/public/data.html'));
        case '/style.css':
            return new Response(200, ['Content-Type' => 'text/css'], file_get_contents(__DIR__ . '/public/style.css'));
        default:
            return new Response(404, ['Content-Type' => 'text/plain'], "404 Not Found");
    }
});

$socket = new SocketServer("0.0.0.0:8080", [], $loop);
$server->listen($socket);
echo "Servidor corriendo en http://0.0.0.0:8080\n";
$loop->run();