<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa\Reader\Sheet;

use DecodeLabs\Atlas\File;
use DecodeLabs\Atlas\Mode;
use DecodeLabs\Mesa\Cell\Raw as RawCell;
use DecodeLabs\Mesa\Reader;
use DecodeLabs\Mesa\Reader\SheetAbstract;
use DecodeLabs\Mesa\Row;

class Csv extends SheetAbstract
{
    protected const string Separator = ",";
    protected const string Enclosure = '"';

    protected File $file;
    protected int $rowCount = 0;

    public static function parseSheets(
        Reader $reader
    ): array {
        return [
            new static($reader, $reader->fileName ?? 'Sheet 1')
        ];
    }

    protected function initializeFromWorkbook(
        Reader $workbook
    ): void {
        $this->file = $workbook->file;
        $this->file->open(Mode::ReadOnly);
    }

    protected function readRow(): ?Row
    {
        if(null === ($line = $this->file->readLine())) {
            return null;
        }

        $output = [];

        foreach(str_getcsv($line, self::Separator, self::Enclosure, '') as $value) {
            if($value === '') {
                $value = null;
            }

            $output[] = new RawCell($value);
        }

        // @phpstan-ignore-next-line
        return new Row($this->rowCount++, $output);
    }

    public function rewind(): void
    {
        $this->file->setPosition(0);
        $this->rowCount = 0;
    }
}
