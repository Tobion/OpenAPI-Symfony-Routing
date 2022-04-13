<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses;

use OpenApi\Attributes as OAT;

class FooController
{
    #[OAT\Get(path:"/foo")]
    #[OAT\Response(response:"200", description:"Success")]
    public function __invoke(): void
    {
    }
}
