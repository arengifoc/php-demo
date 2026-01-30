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
            'build' => $this->getBuildInfo(),
            'deployment' => $this->getDeploymentInfo(),
        ];

        $json = json_encode($health, JSON_PRETTY_PRINT);
        if ($json === false) {
            $json = '{"error":"Error al codificar JSON"}';
        }
        $response->getBody()->write($json);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-Build-Commit', $this->getBuildInfo()['commit'] ?? 'unknown')
            ->withHeader('X-Build-Date', $this->getBuildInfo()['fecha'] ?? 'unknown')
            ->withStatus(200);
    }

    private function getUptime(): string
    {
        $uptime = time() - $_SERVER['REQUEST_TIME'];

        return sprintf('%d segundos', $uptime);
    }

    private function getBuildInfo(): array
    {
        $buildFile = __DIR__ . '/../../build-info.json';
        
        if (file_exists($buildFile)) {
            $content = file_get_contents($buildFile);
            $info = json_decode($content, true);
            return $info ?: $this->getDefaultBuildInfo();
        }
        
        return $this->getDefaultBuildInfo();
    }

    private function getDefaultBuildInfo(): array
    {
        return [
            'commit' => getenv('GIT_COMMIT') ?: 'dev',
            'branch' => getenv('GIT_BRANCH') ?: 'local',
            'fecha' => getenv('BUILD_DATE') ?: date('c'),
            'tag' => getenv('IMAGE_TAG') ?: 'latest',
        ];
    }

    private function getDeploymentInfo(): array
    {
        return [
            'tag' => getenv('DEPLOY_TAG') ?: getenv('IMAGE_TAG') ?: 'latest',
            'environment' => getenv('DEPLOY_ENV') ?: 'unknown',
        ];
    }
}
