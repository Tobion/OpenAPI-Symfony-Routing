<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\OperationId;

use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     @SWG\Info(title="My API", version="1.0")
 * )
 */
class Controller
{
    /**
     * @SWG\Get(
     *     path="/foobar",
     *     operationId="my-name",
     *     @SWG\Response(response="200", description="Success")
     * )
     */
    public function __invoke(): void
    {
    }
}
