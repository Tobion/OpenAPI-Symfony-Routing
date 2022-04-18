<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses;

use OpenApi\Attributes as OA;

class FooController
{
    #[OA\Get(path:"/foo")]
    #[OA\Response(response:"200", description:"Success")]
    public function __invoke(): void
    {
    }
}
