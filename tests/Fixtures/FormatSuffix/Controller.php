<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\FormatSuffix;

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
     *     path="/a",
     *     @SWG\Response(
     *         response="200",
     *         description="Success",
     *     )
     * )
     */
    public function inheritEnabledFormatSuffix(): void
    {
    }

    /**
     * @SWG\Get(
     *     path="/b",
     *     x={"format-pattern": "json|xml"},
     *     @SWG\Response(
     *         response="200",
     *         description="Success",
     *     )
     * )
     */
    public function defineFormatPattern(): void
    {
    }

    /**
     * @SWG\Get(
     *     path="/c",
     *     x={"format-suffix": false},
     *     @SWG\Response(
     *         response="200",
     *         description="Success",
     *     )
     * )
     */
    public function disableFormatSuffix(): void
    {
    }
}
