<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa;

use Stringable;

/**
 * @template T
 */
interface Cell extends Stringable
{
    public ?string $rawValue { get; set; }

    /**
     * @var ?T
     */
    public $value { get; set; }

    /**
     * @param ?T $value
     */
    public static function fromValue(
        mixed $value
    ): static;

    public function __construct(
        ?string $rawValue
    );
}
