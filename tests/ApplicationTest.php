<?php

declare(strict_types=1);

namespace Tests;

use App\Application;
use PHPUnit\Framework\TestCase;
use Slim\App;

class ApplicationTest extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();
    }

    public function testLaAplicacionPuedeSerCreada(): void
    {
        $this->assertInstanceOf(Application::class, $this->application);
    }

    public function testLaAplicacionDevuelveSlimApp(): void
    {
        $app = $this->application->getApp();

        $this->assertInstanceOf(App::class, $app);
    }

    public function testElEndpointRaizDevuelveMensajeDeBienvenida(): void
    {
        $app = $this->application->getApp();

        // Crear solicitud
        $request = $this->createRequest('GET', '/');

        // Procesar solicitud
        $response = $app->handle($request);

        // Verificar respuesta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        // Analizar respuesta JSON
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('mensaje', $data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('fecha_hora', $data);
        $this->assertEquals('Bienvenido a la API de Demostración PHP Slim', $data['mensaje']);
        $this->assertEquals('1.0.0', $data['version']);
    }

    public function testElEndpointHealthDevuelveEstadoDeSalud(): void
    {
        $app = $this->application->getApp();

        // Crear solicitud
        $request = $this->createRequest('GET', '/health');

        // Procesar solicitud
        $response = $app->handle($request);

        // Verificar respuesta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        // Analizar respuesta JSON
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('estado', $data);
        $this->assertArrayHasKey('fecha_hora', $data);
        $this->assertArrayHasKey('tiempo_activo', $data);
        $this->assertArrayHasKey('version_php', $data);
        $this->assertEquals('saludable', $data['estado']);
        $this->assertEquals(PHP_VERSION, $data['version_php']);
    }

    public function testRutaInvalidaDevuelveNoEncontrado(): void
    {
        $app = $this->application->getApp();

        try {
            // Crear solicitud para ruta inexistente
            $request = $this->createRequest('GET', '/invalid-route');

            // Procesar solicitud - lanzará HttpNotFoundException
            $response = $app->handle($request);

            // Si no lanza excepción, verificar 404
            $this->assertEquals(404, $response->getStatusCode());
        } catch (\Slim\Exception\HttpNotFoundException $e) {
            // Esperado - Slim lanza excepción para 404
            $this->assertEquals(404, $e->getCode());
        }
    }

    public function testMetodoInvalidoDevuelveMetodoNoPermitido(): void
    {
        $app = $this->application->getApp();

        try {
            // Intentar POST en endpoint solo GET
            $request = $this->createRequest('POST', '/');

            // Procesar solicitud - lanzará HttpMethodNotAllowedException
            $response = $app->handle($request);

            // Si no lanza excepción, verificar 405
            $this->assertContains($response->getStatusCode(), [404, 405]);
        } catch (\Slim\Exception\HttpMethodNotAllowedException $e) {
            // Esperado - Slim lanza excepción para método no permitido
            $this->assertEquals(405, $e->getCode());
        }
    }

    private function createRequest(string $method, string $path): \Psr\Http\Message\ServerRequestInterface
    {
        $app = $this->application->getApp();
        $factory = $app->getResponseFactory();

        $uri = new \Slim\Psr7\Uri('http', 'localhost', null, $path);
        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Error al abrir flujo');
        }
        $request = new \Slim\Psr7\Request(
            $method,
            $uri,
            new \Slim\Psr7\Headers(),
            [],
            [],
            new \Slim\Psr7\Stream($stream)
        );

        return $request;
    }
}
