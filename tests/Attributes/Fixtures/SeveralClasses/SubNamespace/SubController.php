<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses\SubNamespace;

use OpenApi\Attributes as OA;

class SubController
{
    #[OA\Get(path:"/sub")]
    #[OA\Response(response:"200", description:"Success")]
    public function __invoke(): void
    {
    }
}
