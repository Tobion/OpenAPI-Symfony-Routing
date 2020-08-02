<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\Basic;

use Openapi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(title="My API", version="1.0")
 * )
 */
class Controller
{
    /**
     * @OA\Get(
     *     path="/foobar",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke(): void
    {
    }
}
