<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralRoutesOnOneAction;

use OpenApi\Attributes as OAT;

#[OAT\Info(title:"My API", version:"1.0")]
class Controller
{
    #[OAT\Get(path:"/foobar", responses: [new OAT\Response(response:"200", description:"Success")])]
    #[OAT\Post(path:"/foobar", responses: [new OAT\Response(response:"200", description:"Success")])]
    #[OAT\Get(path:"/foo-bar", operationId: "my-name", responses: [new OAT\Response(response:"200", description:"Success")])]
    public function __invoke(): void
    {
    }
}
