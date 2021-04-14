<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\FormatSuffix;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(title="My API", version="1.0"),
 *     x={"format-suffix": {
 *         "enabled": true
 *     }}
 * )
 */
class Controller
{
    /**
     * @OA\Get(
     *     path="/a",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function inheritEnabledFormatSuffix(): void
    {
    }

    /**
     * @OA\Get(
     *     path="/b",
     *     x={"format-suffix": {
     *         "pattern": "json|xml"
     *     }},
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function defineFormatPattern(): void
    {
    }

    /**
     * @OA\Get(
     *     path="/c",
     *     x={"format-suffix": false},
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function disableFormatSuffix(): void
    {
    }
}
