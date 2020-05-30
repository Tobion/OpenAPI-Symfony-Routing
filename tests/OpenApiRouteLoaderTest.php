<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\Basic\Controller as BasicController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\FormatSuffix\Controller as FormatSuffixController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\OperationId\Controller as OperationIdController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\PathParameterPattern\Controller as PathParameterPatternController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralClasses\BarController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralClasses\FooController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralClasses\SubNamespace\SubController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralHttpMethods\Controller as SeveralHttpMethodsController;
use Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralRoutesOnOneAction\Controller as SeveralRoutesOnOneActionController;

class OpenApiRouteLoaderTest extends TestCase
{
    private const FIXTURES_ROUTE_NAME_PREFIX = 'tobion_openapisymfonyrouting_tests_fixtures_';

    public function testBasic(): void
    {
        $finder = (new Finder())->in(__DIR__.'/Fixtures/Basic');
        $routeLoader = new OpenApiRouteLoader($finder);

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'basic__invoke',
            (new Route('/foobar'))->setMethods('GET')->setDefault('_controller', BasicController::class.'::__invoke')
        );

        $this->assertEquals($expectedRoutes, $routes);
    }

    public function testFormatSuffix(): void
    {
        $finder = (new Finder())->in(__DIR__.'/Fixtures/FormatSuffix');
        $routeLoader = new OpenApiRouteLoader($finder);

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'formatsuffix_inheritenabledformatsuffix',
            (new Route('/a.{_format}'))->setDefault('_format', null)->setMethods('GET')->setDefault('_controller', FormatSuffixController::class.'::inheritEnabledFormatSuffix')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'formatsuffix_defineformatpattern',
            (new Route('/b.{_format}'))->setDefault('_format', null)->setRequirement('_format', 'json|xml')->setMethods('GET')->setDefault('_controller', FormatSuffixController::class.'::defineFormatPattern')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'formatsuffix_disableformatsuffix',
            (new Route('/c'))->setMethods('GET')->setDefault('_controller', FormatSuffixController::class.'::disableFormatSuffix')
        );

        $this->assertEquals($expectedRoutes, $routes);
    }

    public function testOperationId(): void
    {
        $finder = (new Finder())->in(__DIR__.'/Fixtures/OperationId');
        $routeLoader = new OpenApiRouteLoader($finder);

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            'my-name',
            (new Route('/foobar'))->setMethods('GET')->setDefault('_controller', OperationIdController::class.'::__invoke')
        );

        $this->assertEquals($expectedRoutes, $routes);
    }

    public function testPathParameterPattern(): void
    {
        $finder = (new Finder())->in(__DIR__.'/Fixtures/PathParameterPattern');
        $routeLoader = new OpenApiRouteLoader($finder);

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'pathparameterpattern_nopattern',
            (new Route('/foo/{id}'))->setMethods('GET')->setDefault('_controller', PathParameterPatternController::class.'::noPattern')
        );
        // OpenAPI needs the param pattern to be anchored (^$) to have the desired effect. Symfony automatically trims those to get a valid full path regex.
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'pathparameterpattern_withpattern',
            (new Route('/bar/{id}'))->setRequirement('id', '^[a-zA-Z0-9]+$')->setMethods('GET')->setDefault('_controller', PathParameterPatternController::class.'::withPattern')
        );

        $this->assertEquals($expectedRoutes, $routes);
    }

    public function testSeveralClasses(): void
    {
        $finder = (new Finder())->in(__DIR__.'/Fixtures/SeveralClasses')->files();
        $routeLoader = new OpenApiRouteLoader($finder);

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalclasses_bar__invoke',
            (new Route('/bar'))->setMethods('GET')->setDefault('_controller', BarController::class.'::__invoke')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalclasses_foo__invoke',
            (new Route('/foo'))->setMethods('GET')->setDefault('_controller', FooController::class.'::__invoke')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalclasses_subnamespace_sub__invoke',
            (new Route('/sub'))->setMethods('GET')->setDefault('_controller', SubController::class.'::__invoke')
        );

        $this->assertEquals($expectedRoutes, $routes);
    }

    public function testSeveralHttpMethods(): void
    {
        $finder = (new Finder())->in(__DIR__.'/Fixtures/SeveralHttpMethods');
        $routeLoader = new OpenApiRouteLoader($finder);

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalhttpmethods_get',
            (new Route('/foobar'))->setMethods('GET')->setDefault('_controller', SeveralHttpMethodsController::class.'::get')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalhttpmethods_put',
            (new Route('/foobar'))->setMethods('PUT')->setDefault('_controller', SeveralHttpMethodsController::class.'::put')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalhttpmethods_post',
            (new Route('/foobar'))->setMethods('POST')->setDefault('_controller', SeveralHttpMethodsController::class.'::post')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalhttpmethods_delete',
            (new Route('/foobar'))->setMethods('DELETE')->setDefault('_controller', SeveralHttpMethodsController::class.'::delete')
        );

        $this->assertEquals($expectedRoutes, $routes);
    }

    public function testSeveralRoutesOnOneAction(): void
    {
        $finder = (new Finder())->in(__DIR__.'/Fixtures/SeveralRoutesOnOneAction');
        $routeLoader = new OpenApiRouteLoader($finder);

        $routes = $routeLoader->__invoke();

        $expectedRoutes = new RouteCollection();
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalroutesononeaction__invoke',
            (new Route('/foobar'))->setMethods('GET')->setDefault('_controller', SeveralRoutesOnOneActionController::class.'::__invoke')
        );
        $expectedRoutes->add(
            self::FIXTURES_ROUTE_NAME_PREFIX.'severalroutesononeaction__invoke_1',
            (new Route('/foobar'))->setMethods('POST')->setDefault('_controller', SeveralRoutesOnOneActionController::class.'::__invoke')
        );
        $expectedRoutes->add(
            'my-name',
            (new Route('/foo-bar'))->setMethods('GET')->setDefault('_controller', SeveralRoutesOnOneActionController::class.'::__invoke')
        );

        $this->assertEquals($expectedRoutes, $routes);
    }
}
