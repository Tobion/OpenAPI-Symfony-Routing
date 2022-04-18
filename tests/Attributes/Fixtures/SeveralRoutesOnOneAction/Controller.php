<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralRoutesOnOneAction;

use OpenApi\Attributes as OA;

#[OA\Info(title:"My API", version:"1.0")]
class Controller
{
    #[OA\Get(path:"/foobar", responses: [new OA\Response(response:"200", description:"Success")])]
    #[OA\Post(path:"/foobar", responses: [new OA\Response(response:"200", description:"Success")])]
    #[OA\Get(path:"/foo-bar", operationId: "my-name", responses: [new OA\Response(response:"200", description:"Success")])]
    public function __invoke(): void
    {
    }
}
