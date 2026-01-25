<?php

declare(strict_types=1);

namespace Tests\Handler;

use App\Handler\HealthHandler;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;

class HealthHandlerIntegrationTest extends TestCase
{
    private HealthHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new HealthHandler();
    }

    public function testElHealthHandlerDevuelveRespuestaJsonValida(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $result = ($this->handler)($request, $response);

        // Verificar código de estado
        $this->assertEquals(200, $result->getStatusCode());

        // Verificar encabezado de tipo de contenido
        $this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));

        // Parsear y validar JSON
        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertNotNull($data, 'El cuerpo de respuesta debe ser JSON válido');
        $this->assertIsArray($data);
    }

    public function testElHealthHandlerIncluyeTodosLosCamposRequeridos(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $result = ($this->handler)($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        // Verificar que todos los campos requeridos estén presentes
        $this->assertArrayHasKey('estado', $data);
        $this->assertArrayHasKey('fecha_hora', $data);
        $this->assertArrayHasKey('tiempo_activo', $data);
        $this->assertArrayHasKey('version_php', $data);
    }

    public function testElHealthHandlerDevuelveValoresCorrectos(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $result = ($this->handler)($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        // Verificar valores
        $this->assertEquals('saludable', $data['estado']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/', $data['fecha_hora']);
        $this->assertStringContainsString('segundos', $data['tiempo_activo']);
        $this->assertEquals(PHP_VERSION, $data['version_php']);
    }

    public function testElTiempoActivoDelHealthHandlerEsNumerico(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $result = ($this->handler)($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        // Extraer valor numérico de la cadena de tiempo activo
        preg_match('/(\d+)/', $data['tiempo_activo'], $matches);

        $this->assertNotEmpty($matches);
        $this->assertIsNumeric($matches[1]);
        $this->assertGreaterThanOrEqual(0, (int) $matches[1]);
    }

    private function createRequest(): Request
    {
        $uri = new Uri('http', 'localhost', null, '/health');
        $headers = new Headers();
        $streamFactory = new StreamFactory();
        $body = $streamFactory->createStream();

        return new Request('GET', $uri, $headers, [], [], $body);
    }

    private function createResponse(): Response
    {
        return new Response();
    }
}
