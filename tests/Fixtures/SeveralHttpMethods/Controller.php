<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralHttpMethods;

use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     @SWG\Info(
 *         title="My API",
 *         version="1.0"
 *     )
 * )
 */
class Controller
{
    /**
     * @SWG\Get(
     *     path="/foobar",
     *     @SWG\Response(
     *         response="200",
     *         description="Success"
     *     )
     * )
     */
    public function get(): void
    {
    }

    /**
     * @SWG\Put(
     *     path="/foobar",
     *     @SWG\Response(
     *         response="200",
     *         description="Success"
     *     )
     * )
     */
    public function put(): void
    {
    }

    /**
     * @SWG\Post(
     *     path="/foobar",
     *     @SWG\Response(
     *         response="200",
     *         description="Success"
     *     )
     * )
     */
    public function post(): void
    {
    }

    /**
     * @SWG\Delete(
     *     path="/foobar",
     *     @SWG\Response(
     *         response="200",
     *         description="Success"
     *     )
     * )
     */
    public function delete(): void
    {
    }
}
