<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\PathParameterPattern;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: "My API", version: "1.0")]
class Controller
{
    #[OAT\Get(path: "/foo/{id}")]
    #[OAT\Parameter(name:"id", in:"path", required:true, schema: new OAT\Schema(type:"string"))]
    #[OAT\Response(response:"200", description:"Success")]
    public function noPattern(): void
    {
    }

    #[OAT\Get(path: "/baz/{id}")]
    #[OAT\Parameter(name:"id", in:"path", required:true)]
    #[OAT\Response(response:"200", description:"Success")]
    public function noSchema(): void
    {
    }

    #[OAT\Get(path: "/bar/{id}")]
    #[OAT\Parameter(
        name:"id",
        in:"path",
        required:true,
        schema: new OAT\Schema(type:"string", pattern:"^[a-zA-Z0-9]+$")
    )]
    #[OAT\Response(response:"200", description:"Success")]
    public function withPattern(): void
    {
    }
}
