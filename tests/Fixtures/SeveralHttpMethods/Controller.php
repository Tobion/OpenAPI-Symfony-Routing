<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralHttpMethods;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(title="My API", version="1.0")
 * )
 */
class Controller
{
    /**
     * @OA\Get(
     *     path="/foobar",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function get(): void
    {
    }

    /**
     * @OA\Put(
     *     path="/foobar",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function put(): void
    {
    }

    /**
     * @OA\Post(
     *     path="/foobar",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function post(): void
    {
    }

    /**
     * @OA\Delete(
     *     path="/foobar",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function delete(): void
    {
    }
}
