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

class HealthHandlerTest extends TestCase
{
    private HealthHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new HealthHandler();
    }

    public function testElEndpointHealthDevuelveRespuestaExitosa(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $result = ($this->handler)($request, $response);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('estado', $data);
        $this->assertEquals('saludable', $data['estado']);
        $this->assertArrayHasKey('version_php', $data);
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
