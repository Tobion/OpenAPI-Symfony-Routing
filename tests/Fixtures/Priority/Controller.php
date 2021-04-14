<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\Priority;

use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     @SWG\Info(title="My API", version="1.0")
 * )
 */
class Controller
{
    /**
     * @SWG\Get(
     *     path="/foo",
     *     @SWG\Response(response="200", description="Success")
     * )
     */
    public function foo(): void
    {
    }

    /**
     * @SWG\Get(
     *     path="/{catchall}",
     *     x={"priority": -100},
     *     @SWG\Response(response="200", description="Success")
     * )
     */
    public function catchall(): void
    {
    }

    /**
     * @SWG\Get(
     *     path="/bar",
     *     x={"priority": 10},
     *     @SWG\Response(response="200", description="Success")
     * )
     */
    public function bar(): void
    {
    }
}
