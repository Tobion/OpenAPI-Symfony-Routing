<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\OperationId;

use OpenApi\Attributes as OA;

#[OA\Info(title: "My API", version: "1.0")]
class Controller
{
    #[OA\Get(path: "/foobar", operationId: "my-name")]
    #[OA\Response(response: "200", description: "Success")]
    public function __invoke(): void
    {
    }
}
