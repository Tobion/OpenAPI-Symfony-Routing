<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralClasses;

use OpenApi\Attributes as OAT;

#[OAT\Info(title:"My API", version:"1.0")]
class BarController
{
    #[OAT\Get(path:"/bar")]
    #[OAT\Response(response:"200", description:"Success")]
    public function __invoke(): void
    {
    }
}
