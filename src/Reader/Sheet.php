<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa\Reader;

use DecodeLabs\Mesa\AliasMap;
use DecodeLabs\Mesa\Row;
use DecodeLabs\Mesa\Sheet as BaseSheet;
use DecodeLabs\Mesa\Reader;
use Generator;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int,Row>
 */
interface Sheet extends BaseSheet, IteratorAggregate
{
    public function __construct(
        Reader $workbook,
        string $name
    );

    /**
     * @return array<self>
     */
    public static function parseSheets(
        Reader $reader
    ): array;

    public function rewind(): void;

    /**
     * @param array<string>|AliasMap|null $aliases
     * @param ?callable(Row): ?Row $filter
     * @return Generator<int,Row>
     */
    public function scan(
        array|AliasMap|null $aliases = null,
        ?callable $filter = null
    ): Generator;
}
