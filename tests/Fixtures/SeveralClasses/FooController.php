<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralClasses;

use OpenApi\Annotations as OA;

class FooController
{
    /**
     * @OA\Get(
     *     path="/foo",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke(): void
    {
    }
}
