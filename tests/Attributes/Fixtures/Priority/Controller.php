<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\Priority;

use OpenApi\Attributes as OAT;

#[OAT\Info(title:"My API", version:"1.0")]
class Controller
{
    #[OAT\Get(path:"/foo")]
    #[OAT\Response(response:"200", description:"Success")]
    public function foo(): void
    {
    }

    #[OAT\Get(path:"/{catchall}", x: ["priority" => -100])]
    #[OAT\Response(response:"200", description:"Success")]
    public function catchall(): void
    {
    }

    #[OAT\Get(path:"/bar", x: ["priority" => 10])]
    #[OAT\Response(response:"200", description:"Success")]
    public function bar(): void
    {
    }
}
