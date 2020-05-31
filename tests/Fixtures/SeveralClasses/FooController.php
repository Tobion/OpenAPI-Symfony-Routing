<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralClasses;

use Swagger\Annotations as SWG;

class FooController
{
    /**
     * @SWG\Get(
     *     path="/foo",
     *     @SWG\Response(response="200", description="Success")
     * )
     */
    public function __invoke(): void
    {
    }
}
