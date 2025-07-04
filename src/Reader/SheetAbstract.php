<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa\Reader;

use DecodeLabs\Mesa\AliasMap;
use DecodeLabs\Mesa\Row;
use DecodeLabs\Mesa\Reader;
use Generator;
use WeakReference;

abstract class SheetAbstract implements Sheet
{
    protected(set) string $name;

    /**
     * @var WeakReference<Reader>
     */
    protected WeakReference $workbook;

    final public function __construct(
        Reader $workbook,
        string $name
    ) {
        $this->workbook = WeakReference::create($workbook);
        $this->name = $name;

        $this->initializeFromWorkbook($workbook);
    }

    abstract protected function initializeFromWorkbook(
        Reader $workbook
    ): void;

    public function getIterator(): Generator
    {
        return $this->scan();
    }

    abstract protected function readRow(): ?Row;


    public function scan(
        array|AliasMap|null $aliases = null,
        ?callable $filter = null
    ): Generator {
        $this->rewind();

        if(is_array($aliases)) {
            $aliases = new AliasMap($aliases);
        }

        while(null !== ($row = $this->readRow())) {
            if($aliases !== null) {
                $row->aliases = $aliases;
            }

            if($filter !== null) {
                $row = $filter($row);
            }

            if($row === null) {
                continue;
            }

            yield $row;
        }
    }
}
