<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Annotations\Fixtures\PathParameterPattern;

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
     *     path="/foo/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function noPattern(): void
    {
    }

    /**
     * @OA\Get(
     *     path="/baz/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function noSchema(): void
    {
    }

    /**
     * @OA\Get(
     *     path="/bar/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", pattern="^[a-zA-Z0-9]+$")
     *     ),
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function withPattern(): void
    {
    }
}
