<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Annotations\Fixtures\SeveralRoutesOnOneAction;

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
     *
     * @OA\Post(
     *     path="/foobar",
     *     @OA\Response(response="200", description="Success")
     * )
     *
     * @OA\Get(
     *     path="/foo-bar",
     *     operationId="my-name",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function __invoke(): void
    {
    }
}
