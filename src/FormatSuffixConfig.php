<?php

declare(strict_types=1);

namespace Tobion\OpenApiSymfonyRouting;

use OpenApi\Annotations\AbstractAnnotation;

/**
 * @internal
 */
class FormatSuffixConfig
{
    /**
     * @var bool
     */
    public $enabled;

    /**
     * @var string|null
     */
    public $pattern;

    public function __construct(
        bool $enabled,
        ?string $pattern
    ) {
        $this->enabled = $enabled;
        $this->pattern = $pattern;
    }

    public static function fromAnnotation(AbstractAnnotation $annotation, ?self $parent = null): self
    {
        if (!isset($annotation->x['format-suffix'])) {
            return new self($parent ? $parent->enabled : false, $parent ? $parent->pattern : null);
        }

        if (is_bool($annotation->x['format-suffix'])) {
            return new self($annotation->x['format-suffix'], $parent ? $parent->pattern : null);
        }

        return new self(
            $annotation->x['format-suffix']['enabled'] ?? ($parent ? $parent->enabled : false),
            $annotation->x['format-suffix']['pattern'] ?? ($parent ? $parent->pattern : null)
        );
    }
}
