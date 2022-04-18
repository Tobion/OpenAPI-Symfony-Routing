<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\Basic;

use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0", title: "My API")]
class Controller
{
    #[OA\Get(path: "/foobar")]
    #[OA\Response(response: 200, description: "OK")]
    public function __invoke(): void
    {
    }
}
