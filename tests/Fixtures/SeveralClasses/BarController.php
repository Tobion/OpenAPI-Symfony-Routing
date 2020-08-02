<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralClasses;

use Openapi\Annotations as OA;

/**
 * @OA\Swagger(
 *     @OA\Info(title="My API", version="1.0")
 * )
 */
class BarController
{
    /**
     * @OA\Get(
     *     path="/bar",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke(): void
    {
    }
}
