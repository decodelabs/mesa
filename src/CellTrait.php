<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa;

/**
 * @phpstan-require-implements Cell
 */
trait CellTrait
{
    public ?string $rawValue = null;

    public function __construct(
        ?string $rawValue,
    ) {
        $this->rawValue = $rawValue;
    }

    public function __toString(): string
    {
        return $this->rawValue ?? '';
    }
}
