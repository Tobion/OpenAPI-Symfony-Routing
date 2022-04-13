<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses\SubNamespace;

use OpenApi\Attributes as OAT;

class SubController
{
    #[OAT\Get(path:"/sub")]
    #[OAT\Response(response:"200", description:"Success")]
    public function __invoke(): void
    {
    }
}
