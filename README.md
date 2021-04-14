# OpenAPI-Symfony-Routing

Loads routes in Symfony based on [OpenAPI/Swagger annotations](https://github.com/zircote/swagger-php).

[![CI](https://github.com/Tobion/OpenAPI-Symfony-Routing/workflows/CI/badge.svg)](https://github.com/Tobion/OpenAPI-Symfony-Routing/actions)

## Installation

    $ composer require tobion/openapi-symfony-routing

Version >= 1.2 requires zircote/swagger-php 3.x which is compatible with the OpenAPI Specification version 3.
Version < 1.2 requires zircote/swagger-php 2.x which works with the OpenAPI Specification version 2 (fka Swagger).
So tobion/openapi-symfony-routing can be used with both OpenAPI v2 and v3 and composer will select the compatible one for your dependencies.
Route loading stays the same between version. You just need to update the annotations when migrating from OpenAPI v2 to v3.

## Basic Usage

This library allows to (re-)use your OpenAPI documentation to configure the routing of your Symfony-based API.
All the relevant routing information like the HTTP method, path and parameters are already part of the OpenAPI spec.
This way you do not have to duplicate any routing information in Symfony. Consider having the controllers annotated with
[zircote/swagger-php](https://github.com/zircote/swagger-php) like the following example:

```php
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(title="My API", version="1.0")
 * )
 */
class MyController
{
    /**
     * @OA\Get(
     *     path="/foobar",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke()
    {
    }
}
```

This library provides an `OpenApiRouteLoader` that you need to define as service and configure where to look for annotations like so:

```yaml
# config/services.yaml
services:
    Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader:
        autoconfigure: true
        # Looks for OpenAPI/Swagger annotations in the symfony flex default "src" directory
        factory: [Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader, fromSrcDirectory]
```

Then you need to tell Symfony to load routes using it:

```yaml
# config/routes.yaml
openapi_routes:
    resource: Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader
    type: service
```

## Advanced Features

### Scanning annotations in different directories

```yaml
services:
    Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader:
        autoconfigure: true
        factory: [Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader, fromDirectories]
        arguments:
            - '%kernel.project_dir%/src'
            - '%kernel.project_dir%/vendor/acme/my-bundle/src'
```

### Naming routes

By default routes are auto-named based on the controller class and method. If you want to give routes
an explicit name, you can do so using the OpenAPI `operationId` property:

```php
use OpenApi\Annotations as OA;

class MyController
{
    /**
     * @OA\Get(
     *     path="/foobar",
     *     operationId="my-name",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke()
    {
    }
}
```

### Add format suffix automatically

If your API supports different formats it is often common to optionally allow specifying the requested format as a suffix
to the endpoint instead of having to always change headers for content negotiation.
The routing loader allows to add a `.{_format}` placeholder automatically to the routes. This is disabled by default
and can be enabled using a `format-suffix` OpenAPI vendor extension:

```php
use OpenApi\Annotations as OA;

class MyController
{
    /**
     * @OA\Get(
     *     path="/foobar",
     *     x={"format-suffix": {
     *         "enabled": true,
     *         "pattern": "json|xml"
     *     }},
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke()
    {
    }
}
```

The above example will create a route `/foobar.{_format}` where the format is optional and can be json or xml.
You can also enable the format-suffix globally by configuring it on the root OpenApi annotation and disable it for
certain routes again, see [test fixtures](./tests/Fixtures/FormatSuffix/Controller.php).

### Order routes with priority

Since Symfony 5.1, the order of routes defined using annotations can be [influenced using a priority](https://symfony.com/doc/current/routing.html#priority-parameter).
This can be used to make sure templated routes do not match before concrete routes without parameters for the same URL.
The priority can also be set on OpenAPI annotations using a `priority` vendor extension:

```php
use OpenApi\Annotations as OA;

class MyController
{
    /**
     * @OA\Get(
     *     path="/foobar",
     *     x={"priority": 10},
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke()
    {
    }
}
```

## Contributing

To run tests:

    $ composer install
    $ vendor/bin/simple-phpunit
