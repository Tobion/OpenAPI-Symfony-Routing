<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\Basic;

use OpenApi\Attributes as OAT;

#[OAT\Info(version: "1.0", title: "My API")]
class OpenApiSpec
{}

class Controller
{
    #[OAT\Get(path: "/foobar")]
    #[OAT\Response(response: 200, description: "OK")]
    public function __invoke(): void
    {
    }
}
