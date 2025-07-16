<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa;

use DecodeLabs\Archetype;
use DecodeLabs\Atlas;
use DecodeLabs\Atlas\File;
use DecodeLabs\Exceptional;
use DecodeLabs\Mesa\Reader\Sheet;

class Reader implements Workbook
{
    public ?string $fileName = null;
    public ?string $title = null;

    /**
     * @var array<string,Sheet>
     */
    public protected(set) array $sheets = [];

    public protected(set) File $file;

    /**
     * @var class-string<Sheet>
     */
    public protected(set) string $format;

    public ?Sheet $firstSheet {
        get {
            if (empty($this->sheets)) {
                return null;
            }

            return $this->sheets[
                array_key_first($this->sheets)
            ];
        }
    }


    /**
     * @param ?class-string<Sheet> $format
     */
    public static function loadFile(
        string|File $file,
        ?string $format = null
    ): self {
        if (is_string($file)) {
            $file = Atlas::file($file);
        }

        if (!$file->exists()) {
            throw Exceptional::NotFound(
                message: 'File not found',
                data: [
                    'file' => $file
                ]
            );
        }

        return new self($file, $format);
    }

    public static function loadString(
        string $string,
        ?string $format = null,
        ?string $fileName = null
    ): self {
        $file = Atlas::createMemoryFile($string, $fileName ?? 'temp');
        return new self($file, $format);
    }


    protected function __construct(
        File $file,
        ?string $format = null
    ) {
        $this->file = $file;
        $this->fileName = basename($file->path);
        $this->format = $this->detectFormat($this->fileName, $format);

        foreach ($this->format::parseSheets($this) as $sheet) {
            $this->sheets[$sheet->name] = $sheet;
        }
    }

    /**
     * @return class-string<Sheet>
     */
    private function detectFormat(
        ?string $fileName,
        ?string $format = null
    ): string {
        if (!is_string($format)) {
            if ($fileName === null) {
                throw Exceptional::InvalidArgument(
                    message: 'File name or format must be provided'
                );
            }

            $format = pathinfo($fileName, PATHINFO_EXTENSION);
        }

        return Archetype::resolve(Sheet::class, ucfirst(strtolower($format)));
    }


    public function getSheet(
        string $name
    ): ?Sheet {
        return $this->sheets[$name] ?? null;
    }
}
