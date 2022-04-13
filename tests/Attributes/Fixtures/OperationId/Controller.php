<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\OperationId;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: "My API", version: "1.0")]
class Controller
{
    #[OAT\Get(path: "/foobar", operationId: "my-name")]
    #[OAT\Response(response: "200", description: "Success")]
    public function __invoke(): void
    {
    }
}
