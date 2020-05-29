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
     * @var Finder
     */
    private $finder;

    /**
     * @var bool
     */
    private $addFormatSuffix;

    /**
     * @var string|null
     */
    private $defaultFormatPattern;

    /**
     * @var array<string, int>
     */
    private $routeNames = [];

    public function __construct(
        ?Finder $finder = null,
        bool $addFormatSuffix = false,
        ?string $defaultFormatPattern = null
    ) {
        if (null === $finder) {
            // try to use the symfony flex default src directory based on a composer install
            $srcDir = __DIR__.'/../../../../src';
            $realPath = realpath($srcDir);
            if (!$realPath || !is_dir($realPath)) {
                throw new \LogicException(sprintf('The default directory to look for OpenAPI/Swagger annotations "%s" does not exist. Please configure the finder explicitly.'));
            }

            $finder = (new Finder())->in($realPath)->files()->name('*.php')->sortByName()->followLinks();
        }

        $this->finder = $finder;
        $this->addFormatSuffix = $addFormatSuffix;
        $this->defaultFormatPattern = $defaultFormatPattern;
    }

    public function __invoke(): RouteCollection
    {
        $fullSwagger = \Swagger\scan($this->finder);
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
        $formatSuffix = $operation->x['format-suffix'] ?? $this->addFormatSuffix;
        $path = $formatSuffix ? $operation->path.'.{_format}' : $operation->path;
        $route = new Route($path);
        $route->setMethods($operation->method);
        $route->setDefault('_controller', $controller);
        if ($formatSuffix) {
            $formatPattern = $operation->x['format-pattern'] ?? $this->defaultFormatPattern;
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
