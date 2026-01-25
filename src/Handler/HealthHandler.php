<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HealthHandler
{
    public function __invoke(Request $request, Response $response): Response
    {
        $health = [
            'estado' => 'saludable',
            'fecha_hora' => date('c'),
            'tiempo_activo' => $this->getUptime(),
            'version_php' => PHP_VERSION,
        ];

        $json = json_encode($health);
        if ($json === false) {
            $json = '{"error":"Failed to encode JSON"}';
        }
        $response->getBody()->write($json);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    private function getUptime(): string
    {
        $uptime = time() - $_SERVER['REQUEST_TIME'];

        return sprintf('%d segundos', $uptime);
    }
}
