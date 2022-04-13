<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\Basic\Controller as BasicController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\FormatSuffix\Controller as FormatSuffixController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\OperationId\Controller as OperationIdController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\PathParameterPattern\Controller as PathParameterPatternController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\Priority\Controller as PriorityController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses\BarController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses\FooController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses\SubNamespace\SubController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralHttpMethods\Controller as SeveralHttpMethodsController;
use Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralRoutesOnOneAction\Controller as SeveralRoutesOnOneActionController;

class OpenApiRouteLoaderAttributesTest extends TestCase
{
    public function testBasic(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/Basic');

        $routes = $routeLoader->__invoke();

        foreach ($routes as $route) {
            $this->assertEquals(
                $route,
                (new Route('/foobar'))
                    ->setMethods('GET')
                    ->setDefault('_controller', BasicController::class.'::__invoke')
            );
        }
    }

    public function testFormatSuffix(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/FormatSuffix');

        $routes = $routeLoader->__invoke();

        $expectedRoutes = [];
        $expectedRoutes[] = (new Route('/a.{_format}'))
            ->setDefault('_format', null)
            ->setMethods('GET')
            ->setDefault('_controller', FormatSuffixController::class.'::inheritEnabledFormatSuffix');
        $expectedRoutes[] = (new Route('/b.{_format}'))
            ->setDefault('_format', null)
            ->setRequirement('_format', 'json|xml')
            ->setMethods('GET')
            ->setDefault('_controller', FormatSuffixController::class.'::defineFormatPattern');
        $expectedRoutes[] = (new Route('/c'))
            ->setMethods('GET')
            ->setDefault('_controller', FormatSuffixController::class.'::disableFormatSuffix');

        $index = 0;
        foreach ($routes as $route) {
            $this->assertEquals($route, $expectedRoutes[$index++]);
        }
    }

    public function testOperationId(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/OperationId');

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            'my-name',
            (new Route('/foobar'))->setMethods('GET')->setDefault('_controller', OperationIdController::class.'::__invoke')
        );

        self::assertEquals($expectedRoutes, $routes);
    }

    public function testPathParameterPattern(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/PathParameterPattern');

        $routes = $routeLoader->__invoke();

        $expectedRoutes = [];
        $expectedRoutes[] = (new Route('/foo/{id}'))
            ->setMethods('GET')
            ->setDefault('_controller', PathParameterPatternController::class.'::noPattern');
        $expectedRoutes[] = (new Route('/baz/{id}'))
            ->setMethods('GET')
            ->setDefault('_controller', PathParameterPatternController::class.'::noSchema');
        $expectedRoutes[] = (new Route('/bar/{id}'))
            ->setRequirement('id', '^[a-zA-Z0-9]+$')
            ->setMethods('GET')
            ->setDefault('_controller', PathParameterPatternController::class.'::withPattern');

        $index = 0;
        foreach ($routes as $route) {
            $this->assertEquals($route, $expectedRoutes[$index++]);
        }
    }

    public function testPriority(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/Priority');

        $routes = $routeLoader->__invoke();

        $expectedRoutes = [];
        $expectedRoutes[] = (new Route('/bar'))
            ->setMethods('GET')
            ->setDefault('_controller', PriorityController::class.'::bar');
        $expectedRoutes[] = (new Route('/foo'))
            ->setMethods('GET')
            ->setDefault('_controller', PriorityController::class.'::foo');
        $expectedRoutes[] = (new Route('/{catchall}'))
            ->setMethods('GET')
            ->setDefault('_controller', PriorityController::class.'::catchall');

        $index = 0;
        foreach ($routes as $route) {
            $this->assertEquals($route, $expectedRoutes[$index++]);
        }
    }

    public function testSeveralClasses(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/SeveralClasses');

        $routes = $routeLoader->__invoke();

        $expectedRoutes = [];
        $expectedRoutes[] = (new Route('/bar'))
            ->setMethods('GET')
            ->setDefault('_controller', BarController::class.'::__invoke');
        $expectedRoutes[] = (new Route('/foo'))
            ->setMethods('GET')
            ->setDefault('_controller', FooController::class.'::__invoke');
        $expectedRoutes[] = (new Route('/sub'))
            ->setMethods('GET')
            ->setDefault('_controller', SubController::class.'::__invoke');

        $index = 0;
        foreach ($routes as $route) {
            $this->assertEquals($route, $expectedRoutes[$index++]);
        }
    }

    public function testSeveralHttpMethods(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/SeveralHttpMethods');

        $routes = $routeLoader->__invoke();

        $expectedRoutes = [];
        $expectedRoutes[] = (new Route('/foobar'))
            ->setMethods('GET')
            ->setDefault('_controller', SeveralHttpMethodsController::class.'::get');
        $expectedRoutes[] = (new Route('/foobar'))
            ->setMethods('PUT')
            ->setDefault('_controller', SeveralHttpMethodsController::class.'::put');
        $expectedRoutes[] = (new Route('/foobar'))
            ->setMethods('POST')
            ->setDefault('_controller', SeveralHttpMethodsController::class.'::post');
        $expectedRoutes[] = (new Route('/foobar'))
            ->setMethods('DELETE')
            ->setDefault('_controller', SeveralHttpMethodsController::class.'::delete');

        $index = 0;
        foreach ($routes as $route) {
            $this->assertEquals($route, $expectedRoutes[$index++]);
        }
    }

    public function testSeveralRoutesOnOneAction(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(__DIR__.'/Fixtures/SeveralRoutesOnOneAction');

        $routes = $routeLoader->__invoke();

        $expectedRoutes = [];
        $expectedRoutes[] = (new Route('/foobar'))
            ->setMethods('GET')
            ->setDefault('_controller', SeveralRoutesOnOneActionController::class.'::__invoke');
        $expectedRoutes[] = (new Route('/foobar'))
            ->setMethods('POST')
            ->setDefault('_controller', SeveralRoutesOnOneActionController::class.'::__invoke');
        $expectedRoutes[] = (new Route('/foo-bar'))
            ->setMethods('GET')
            ->setDefault('_controller', SeveralRoutesOnOneActionController::class.'::__invoke');

        $index = 0;
        foreach ($routes as $route) {
            $this->assertEquals($route, $expectedRoutes[$index++]);
        }
    }

    public function testSeveralDirectories(): void
    {
        $routeLoader = OpenApiRouteLoader::fromDirectories(
            __DIR__.'/Fixtures/Basic',
            __DIR__.'/Fixtures/SeveralClasses/SubNamespace'
        );

        $routes = $routeLoader->__invoke();

        $expectedRoutes = [];
        $expectedRoutes[] = (new Route('/foobar'))
            ->setMethods('GET')
            ->setDefault('_controller', BasicController::class.'::__invoke');
        $expectedRoutes[] = (new Route('/sub'))
            ->setMethods('GET')
            ->setDefault('_controller', SubController::class.'::__invoke');

        $index = 0;
        foreach ($routes as $route) {
            $this->assertEquals($route, $expectedRoutes[$index++]);
        }
    }

    public function testSrcDirectoryDoesNotExist(): void
    {
        self::expectException(DirectoryNotFoundException::class);
        self::expectExceptionMessage('/../../../../src" directory does not exist');

        OpenApiRouteLoader::fromSrcDirectory();
    }
}
