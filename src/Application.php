<?php

declare(strict_types=1);

namespace App;

use App\Handler\HealthHandler;
use DI\Container;
use Slim\Factory\AppFactory;

class Application
{
    /** @var \Slim\App<\Psr\Container\ContainerInterface|null> */
    private \Slim\App $app;

    public function __construct()
    {
        $container = new Container();
        AppFactory::setContainer($container);

        $this->app = AppFactory::create();
        $this->registerRoutes();
        $this->registerMiddleware();
    }

    private function registerRoutes(): void
    {
        $this->app->get('/health', HealthHandler::class);

        // ❌ DEMO: Descomentar para simular error de PHP-CS-Fixer (formato incorrecto)
        $this->app->get('/bad-format', function($request,$response){return $response;});

        $this->app->get('/', function ($request, $response) {
            $response->getBody()->write(json_encode([
                'mensaje' => 'Bienvenido a la API de Demostración PHP Slim',
                'version' => '1.0.0',
                'fecha_hora' => date('c'),
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        });
    }

    private function registerMiddleware(): void
    {
        $this->app->addErrorMiddleware(true, true, true);
    }

    public function run(): void
    {
        $this->app->run();
    }

    /**
     * @return \Slim\App<\Psr\Container\ContainerInterface|null>
     */
    public function getApp(): \Slim\App
    {
        return $this->app;
    }
}
