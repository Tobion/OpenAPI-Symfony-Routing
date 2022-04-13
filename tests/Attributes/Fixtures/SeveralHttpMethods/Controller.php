<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Attributes\Fixtures\SeveralHttpMethods;

use OpenApi\Attributes as OAT;

#[OAT\Info(title:"My API", version:"1.0")]
class Controller
{
    #[OAT\Get(path:"/foobar")]
    #[OAT\Response(response:"200", description:"Success")]
    public function get(): void
    {
    }

    #[OAT\Put(path:"/foobar")]
    #[OAT\Response(response:"200", description:"Success")]
    public function put(): void
    {
    }

    #[OAT\Post(path:"/foobar")]
    #[OAT\Response(response:"200", description:"Success")]
    public function post(): void
    {
    }

    #[OAT\Delete(path:"/foobar")]
    #[OAT\Response(response:"200", description:"Success")]
    public function delete(): void
    {
    }
}
