<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\FormatSuffix;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    info: new OAT\Info(
        title: "My API",
        version: "1.0"
    ),
    x: [
        "format-suffix" => [
            "enabled" => true
        ]
    ]
)]
class Controller
{
    #[OAT\Get(path: "/a",)]
    #[OAT\Response(response: "200", description: "Success")]
    public function inheritEnabledFormatSuffix(): void
    {
    }

    #[OAT\Get(path: "/b", x: ["format-suffix" => ["pattern" => "json|xml"]])]
    #[OAT\Response(response: "200", description: "Success")]
    public function defineFormatPattern(): void
    {
    }

    #[OAT\Get(path: "/c", x: ["format-suffix" => false])]
    #[OAT\Response(response: "200", description: "Success")]
    public function disableFormatSuffix(): void
    {
    }
}
