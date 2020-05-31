# OpenAPI-Symfony-Routing

Loads routes in Symfony based on [OpenAPI/Swagger annotations](https://github.com/zircote/swagger-php).

![CI](https://github.com/Tobion/OpenAPI-Symfony-Routing/workflows/CI/badge.svg)

## Installation

    $ composer require tobion/openapi-symfony-routing

## Basic Usage

This library allows to (re-)use your OpenAPI documentation to configure the routing of your Symfony-based API.
All the relevant routing information like the HTTP method, path and parameters are already part of the OpenAPI spec.
This way you do not have to duplicate any routing information in Symfony. Consider having the controllers annotated with
[zircote/swagger-php](https://github.com/zircote/swagger-php) like the following example:

```php
use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     @SWG\Info(title="My API", version="1.0")
 * )
 */
class MyController
{
    /**
     * @SWG\Get(
     *     path="/foobar",
     *     @SWG\Response(response="200", description="Success")
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
    _defaults:
        autoconfigure: true

    Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader:
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
        factory: [Tobion\OpenApiSymfonyRouting\OpenApiRouteLoader, fromDirectories]
        arguments:
            - '%kernel.project_dir%/src'
            - '%kernel.project_dir%/vendor/acme/my-bundle/src'
```

### Naming routes

By default routes are auto-named based on the controller class and method. If you want to give routes
an explicit name, you can do so using the OpenAPI `operationId` property:

```php
use Swagger\Annotations as SWG;

class MyController
{
    /**
     * @SWG\Get(
     *     path="/foobar",
     *     operationId="my-name",
     *     @SWG\Response(response="200", description="Success")
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
use Swagger\Annotations as SWG;

class MyController
{
    /**
     * @SWG\Get(
     *     path="/foobar",
     *     x={"format-suffix": {
     *         "enabled": true,
     *         "pattern": "json|xml"
     *     }},
     *     @SWG\Response(response="200", description="Success")
     * )
     */
    public function __invoke()
    {
    }
}
```

The above example will create a route `/foobar.{_format}` where the format is optional and can be json or xml.
You can also enable the format-suffix globally by configuring it on the root Swagger annotation and disable it for
certain routes again, see [test fixtures](./tests/Fixtures/FormatSuffix/Controller.php).

## Contributing

To run tests:

    $ composer install
    $ vendor/bin/simple-phpunit
