<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting\Tests\Fixtures\SeveralClasses\SubNamespace;

use Swagger\Annotations as SWG;

class SubController
{
    /**
     * @SWG\Get(
     *     path="/sub",
     *     @SWG\Response(
     *         response="200",
     *         description="Success",
     *     )
     * )
     */
    public function __invoke(): void
    {
    }
}
