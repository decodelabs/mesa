<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa;

use ArrayAccess;
use DecodeLabs\Exceptional;
use DecodeLabs\Mesa\Cell\Raw as RawCell;
use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;

/**
 * @implements ArrayAccess<int,Cell<mixed>>
 */
class Row implements ArrayAccess, Dumpable
{
    public int $index;

    /**
     * @var list<Cell<mixed>>
     */
    protected array $cells;

    public ?AliasMap $aliases = null;

    /**
     * @param list<Cell<mixed>> $cells
     */
    public function __construct(
        int $index,
        array $cells,
        ?AliasMap $aliases = null
    ) {
        $this->index = $index;
        $this->cells = $cells;
        $this->aliases = $aliases;
    }

    public function get(
        int|string $index
    ): mixed {
        $index = $this->normalizeIndex($index);
        return ($this->cells[$index] ?? null)?->value;
    }

    public function getRaw(
        int|string $index
    ): ?string {
        $index = $this->normalizeIndex($index);
        return ($this->cells[$index] ?? null)?->rawValue;
    }

    /**
     * @return ?Cell<mixed>
     */
    public function getCell(
        int|string $index
    ): ?Cell {
        $index = $this->normalizeIndex($index);
        return $this->cells[$index] ?? null;
    }

    public function has(
        int|string $index
    ): bool {
        $index = $this->normalizeIndex($index);
        return
            isset($this->cells[$index]) &&
            null !== $this->cells[$index]->rawValue;
    }

    protected function normalizeIndex(
        int|string $index
    ): int {
        if (is_int($index)) {
            return $index;
        }

        if (null !== ($alias = $this->aliases?->get($index))) {
            return $alias;
        }

        throw Exceptional::InvalidArgument(
            'Invalid index: ' . $index
        );
    }

    public function isEmpty(): bool
    {
        foreach ($this->cells as $cell) {
            if (null !== $cell->rawValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int|string $offset
     */
    public function offsetExists(
        mixed $offset
    ): bool {
        $offset = $this->normalizeIndex($offset);
        return isset($this->cells[$offset]);
    }

    /**
     * @param int|string $offset
     * @return ?Cell<mixed>
     */
    public function offsetGet(
        mixed $offset
    ): mixed {
        $offset = $this->normalizeIndex($offset);
        return $this->cells[$offset] ?? null;
    }

    /**
     * @param int|string $offset
     * @param string|Cell<mixed> $value
     */
    public function offsetSet(
        mixed $offset,
        mixed $value
    ): void {
        $offset = $this->normalizeIndex($offset);

        if (!$value instanceof Cell) {
            $value = RawCell::fromValue($value);
        }

        if ($offset < 0) {
            throw Exceptional::InvalidArgument(
                'Offset must be greater than 0'
            );
        }

        if ($offset >= count($this->cells)) {
            for ($i = count($this->cells); $i <= $offset; $i++) {
                $this->cells[] = new RawCell(null);
            }
        }

        // @phpstan-ignore-next-line
        $this->cells[$offset] = $value;
    }

    /**
     * @param int|string $offset
     */
    public function offsetUnset(
        mixed $offset
    ): void {
        $offset = $this->normalizeIndex($offset);

        if (isset($this->cells[$offset])) {
            // @phpstan-ignore-next-line
            $this->cells[$offset] = new RawCell(null);
        }
    }


    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);
        $values = [];

        foreach ($this->cells as $index => $cell) {
            $alias = $this->aliases?->resolve($index) ?? $index;
            $values[$alias] = $cell->value;
        }

        $entity->itemName = (string)$this->index;
        $entity->values = $values;
        return $entity;
    }
}
