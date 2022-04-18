<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\PathParameterPattern;

use mysql_xdevapi\Schema;
use OpenApi\Attributes as OA;

#[OA\Info(title: "My API", version: "1.0")]
class Controller
{
    #[OA\Get(path: "/foo/{id}")]
    #[OA\Response(response:"200", description:"Success")]
    public function noPattern(
        #[OA\Parameter]string $id
    ): void
    {
    }

    #[OA\Get(path: "/baz/{id}")]
    #[OA\Response(response:"200", description:"Success")]
    public function noSchema(
        #[OA\Parameter]string $id
    ): void
    {
    }

    #[OA\Get(path: "/bar/{id}/{type}")]
    #[OA\Response(response:"200", description:"Success")]
    public function withPattern(
        #[OA\PathParameter(required: true, schema: new OA\Schema(pattern:"^[a-zA-Z0-9]+$"))]
        string $id,
        #[OA\PathParameter(required: true, schema: new OA\Schema(enum: ["internal", "external"]))]
        string $type
    ): void
    {
    }
}
