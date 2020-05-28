<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting;

use Swagger\Annotations\Operation;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class OpenApiRouteLoader implements RouteLoaderInterface
{
    /**
     * @var string[]
     */
    private $sourceDirectories;

    /**
     * @var string
     */
    private $sourcePattern;

    /**
     * @var array<string, int>
     */
    private $routeNames = [];

    /**
     * @param string[] $sourceDirectories
     */
    public function __construct(
        array $sourceDirectories,
        string $sourcePattern = '/\.php/'
    ) {
        $this->sourceDirectories = $sourceDirectories;
        $this->sourcePattern = $sourcePattern;
    }

    public function __invoke(): RouteCollection
    {
        $finder = new Finder();
        $finder->in($this->sourceDirectories)->path($this->sourcePattern);

        $fullSwagger = \Swagger\scan($finder);
        $routeCollection = new RouteCollection();

        foreach ($fullSwagger->paths as $path) {
            $this->addRouteFromSwaggerOperation($routeCollection, $path->get);
            $this->addRouteFromSwaggerOperation($routeCollection, $path->put);
            $this->addRouteFromSwaggerOperation($routeCollection, $path->post);
            $this->addRouteFromSwaggerOperation($routeCollection, $path->delete);
            $this->addRouteFromSwaggerOperation($routeCollection, $path->options);
            $this->addRouteFromSwaggerOperation($routeCollection, $path->head);
            $this->addRouteFromSwaggerOperation($routeCollection, $path->patch);
        }

        $this->routeNames = [];

        return $routeCollection;
    }

    private function addRouteFromSwaggerOperation(RouteCollection $routeCollection, ?Operation $operation): void
    {
        if (null === $operation) {
            return;
        }

        $controller = $this->getControllerFromSwaggerOperation($operation);
        $name = $this->getRouteName($operation, $controller);
        $route = $this->createRoute($operation, $controller);
        $routeCollection->add($name, $route);
    }

    private function createRoute(Operation $operation, string $controller): Route
    {
        $formatSuffix = $operation->x['format-suffix'] ?? true;
        $path = $formatSuffix ? $operation->path.'.{_format}' : $operation->path;
        $route = new Route($path);
        $route->setMethods($operation->method);
        $route->setDefault('_controller', $controller);
        if ($formatSuffix) {
            $formatPattern = $operation->x['format-pattern'] ?? 'json|xml';
            $route->setDefault('_format', null);
            $route->setRequirement('_format', $formatPattern);
        }
        if (null !== $operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if ('path' === $parameter->in && null !== $parameter->pattern) {
                    $route->setRequirement($parameter->name, $parameter->pattern);
                }
            }
        }

        return $route;
    }

    private function getControllerFromSwaggerOperation(Operation $operation): string
    {
        $classOrService = ltrim($operation->_context->fullyQualifiedName($operation->_context->class), '\\');

        return $classOrService.'::'.$operation->_context->method;
    }

    private function getRouteName(Operation $operation, string $controller): string
    {
        return \Swagger\UNDEFINED === $operation->operationId ? $this->getDefaultRouteName($controller) : $operation->operationId;
    }

    /**
     * @see \Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader::getDefaultRouteName
     */
    private function getDefaultRouteName(string $controller): string
    {
        $name = str_replace(['\\', '::'], '_', $controller);
        $name = \function_exists('mb_strtolower') && preg_match('//u', $name) ? mb_strtolower($name, 'UTF-8') : strtolower($name);

        $name = preg_replace([
            '/(bundle|controller)_/',
            '/action(_\d+)?$/',
            '/__/',
        ], [
            '_',
            '\\1',
            '_',
        ], $name);

        // handle several routes for the same controller
        if (isset($this->routeNames[$name])) {
            ++$this->routeNames[$name];

            $name .= '_'.$this->routeNames[$name];
        } else {
            $this->routeNames[$name] = 0;
        }

        return $name;
    }
}
