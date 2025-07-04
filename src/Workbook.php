<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa;

interface Workbook
{
    public ?string $fileName { get; }
    public ?string $title { get; }

    /**
     * @var array<string,Sheet>
     */
    public array $sheets { get; }
    public ?Sheet $firstSheet { get; }

    public function getSheet(
        string $name
    ): ?Sheet;
}
