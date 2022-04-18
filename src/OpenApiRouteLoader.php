<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting;

use OpenApi\Analysis;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Operation;
use OpenApi\Generator;
use OpenApi\Processors\DocBlockDescriptions;
use OpenApi\Processors\OperationId;
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
     * @var array<string, int>
     */
    private $routeNames = [];

    /**
     * @var null|Generator
     */
    private $generator = null;

    /**
     * @var string
     */
    private static $openApiUndefined;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        if (!isset(self::$openApiUndefined)) {
            self::$openApiUndefined = \class_exists(Generator::class) ? Generator::UNDEFINED : \OpenApi\UNDEFINED;
        }
    }

    public static function fromDirectories(string $dir, string ...$moreDirs): self
    {
        return new self(
            (new Finder())->in($dir)->in($moreDirs)->files()->name('*.php')->sortByName()->followLinks()
        );
    }

    /**
     * Looks for OpenAPI/Swagger annotations in the symfony flex default "src" directory based on a composer install.
     */
    public static function fromSrcDirectory(): self
    {
        return self::fromDirectories(__DIR__.'/../../../../src');
    }

    public function __invoke(): RouteCollection
    {
        $openApi = $this->createOpenApi();
        $routeCollection = new RouteCollection();

        $globalFormatSuffixConfig = FormatSuffixConfig::fromAnnotation($openApi);

        foreach ($openApi->paths as $path) {
            $pathFormatSuffixConfig = FormatSuffixConfig::fromAnnotation($path, $globalFormatSuffixConfig);

            $this->addRouteFromOpenApiOperation($routeCollection, $path->get, $pathFormatSuffixConfig);
            $this->addRouteFromOpenApiOperation($routeCollection, $path->put, $pathFormatSuffixConfig);
            $this->addRouteFromOpenApiOperation($routeCollection, $path->post, $pathFormatSuffixConfig);
            $this->addRouteFromOpenApiOperation($routeCollection, $path->delete, $pathFormatSuffixConfig);
            $this->addRouteFromOpenApiOperation($routeCollection, $path->options, $pathFormatSuffixConfig);
            $this->addRouteFromOpenApiOperation($routeCollection, $path->head, $pathFormatSuffixConfig);
            $this->addRouteFromOpenApiOperation($routeCollection, $path->patch, $pathFormatSuffixConfig);
        }

        $this->routeNames = [];

        return $routeCollection;
    }

    private function createOpenApi(): OpenApi
    {
        if (!\class_exists(Generator::class)) {
           return \OpenApi\scan($this->finder);
        }

        if (null !== $this->generator) {
            return $this->generator->generate($this->finder);
        }

        if (method_exists(Analysis::class, 'processors')) {
            $processors = array_filter(Analysis::processors(), static function ($processor): bool {
                // remove OperationId processor which would hash the controller starting in 3.2.2 breaking the default route name logic
                return !$processor instanceof OperationId && !$processor instanceof DocBlockDescriptions;
            });

            $this->generator = (new Generator())->setProcessors($this->filterProcessors($processors));

            return $this->generator->generate($this->finder);
        }

        $this->generator = new Generator();

        $this->generator->setProcessors(
            $this->filterProcessors(
                $this->generator->getProcessors()
            )
        );

        return $this->generator->generate($this->finder);
    }

    /**
     * @param Operation|string $operation
     */
    private function addRouteFromOpenApiOperation(RouteCollection $routeCollection, $operation, FormatSuffixConfig $parentFormatSuffixConfig): void
    {
        if (self::$openApiUndefined === $operation || !$operation instanceof Operation) {
            return;
        }

        $controller = $this->getControllerFromOpenApiOperation($operation);
        $name = $this->getRouteName($operation, $controller);
        $route = $this->createRoute($operation, $controller, $parentFormatSuffixConfig);
        $priority = $this->getRoutePriority($operation);
        $routeCollection->add($name, $route, $priority);
    }

    private function createRoute(Operation $operation, string $controller, FormatSuffixConfig $parentFormatSuffixConfig): Route
    {
        $formatSuffixConfig = FormatSuffixConfig::fromAnnotation($operation, $parentFormatSuffixConfig);

        $path = $formatSuffixConfig->enabled ? $operation->path.'.{_format}' : $operation->path;
        $route = new Route($path);
        $route->setMethods($operation->method);
        $route->setDefault('_controller', $controller);

        if ($formatSuffixConfig->enabled) {
            $route->setDefault('_format', null);

            if (null !== $formatSuffixConfig->pattern) {
                $route->setRequirement('_format', $formatSuffixConfig->pattern);
            }
        }
        if (self::$openApiUndefined !== $operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if ('path' === $parameter->in && self::$openApiUndefined !== $parameter->schema) {
                    if (self::$openApiUndefined !== $parameter->schema->pattern) {
                        $route->setRequirement($parameter->name, $parameter->schema->pattern);
                    }

                    if (self::$openApiUndefined !== $parameter->schema->enum) {
                        $route->setRequirement($parameter->name, implode('|', $parameter->schema->enum));
                    }
                }
            }
        }

        return $route;
    }

    private function getControllerFromOpenApiOperation(Operation $operation): string
    {
        $classOrService = ltrim($operation->_context->fullyQualifiedName($operation->_context->class), '\\');

        return $classOrService.'::'.$operation->_context->method;
    }

    private function getRouteName(Operation $operation, string $controller): string
    {
        // swagger-php v3 adds the controller as operationId automatically, see \OpenApi\Processors\OperationId.
        // This must be ignored as it is not viable with multiple annotations on the same controller.

        return self::$openApiUndefined === $operation->operationId || $controller === $operation->operationId ? $this->getDefaultRouteName($controller) : $operation->operationId;
    }

    private function getRoutePriority(Operation $operation): int
    {
        if (isset($operation->x['priority']) && is_int($operation->x['priority'])) {
            return $operation->x['priority'];
        }

        return 0;
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

    private function filterProcessors(array $processors): array
    {
        return array_filter($processors, static function ($processor): bool {
            return !$processor instanceof OperationId && !$processor instanceof DocBlockDescriptions;
        });
    }
}
