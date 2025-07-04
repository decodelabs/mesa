<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa\Cell;

use DecodeLabs\Coercion;
use DecodeLabs\Mesa\Cell;
use DecodeLabs\Mesa\CellTrait;

/**
 * @implements Cell<string>
 */
class Raw implements Cell
{
    use CellTrait;

    public $value {
        get => $this->rawValue;
        set {
            $this->rawValue = Coercion::asString($value);
        }
    }

    public static function fromValue(
        mixed $value
    ): static {
        return new static($value);
    }
}
