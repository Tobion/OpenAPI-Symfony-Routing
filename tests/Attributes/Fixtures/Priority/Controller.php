<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\Priority;

use OpenApi\Attributes as OA;

#[OA\Info(title:"My API", version:"1.0")]
class Controller
{
    #[OA\Get(path:"/foo")]
    #[OA\Response(response:"200", description:"Success")]
    public function foo(): void
    {
    }

    #[OA\Get(path:"/{catchall}", x: ["priority" => -100])]
    #[OA\Response(response:"200", description:"Success")]
    public function catchall(): void
    {
    }

    #[OA\Get(path:"/bar", x: ["priority" => 10])]
    #[OA\Response(response:"200", description:"Success")]
    public function bar(): void
    {
    }
}
