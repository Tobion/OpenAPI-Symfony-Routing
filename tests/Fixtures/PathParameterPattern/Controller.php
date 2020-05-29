<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\PathParameterPattern;

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
     *     path="/foo/{id}",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="string",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success",
     *     )
     * )
     */
    public function noPattern(): void
    {
    }

    /**
     * @SWG\Get(
     *     path="/bar/{id}",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="string",
     *         required=true,
     *         pattern="^[a-zA-Z0-9]+$"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success",
     *     )
     * )
     */
    public function withPattern(): void
    {
    }
}
