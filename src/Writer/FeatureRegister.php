<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa\Writer;

use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;
use Generator;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<string,bool>
 */
class FeatureRegister implements
    Dumpable,
    IteratorAggregate
{
    /**
     * @var array<string,bool>
     */
    public protected(set) array $features = [];

    /**
     * @param array<string,bool> $features
     */
    public function __construct(
        array $features = []
    ) {
        $this->features = $features;
    }

    public function enable(
        string $feature
    ): void {
        $this->features[$feature] = true;
    }

    public function disable(
        string $feature
    ): void {
        $this->features[$feature] = false;
    }

    public function has(
        string $feature,
        bool $default = true
    ): bool {
        return $this->features[$feature] ?? $default;
    }

    /**
     * @return Generator<string,bool>
     */
    public function getIterator(): Generator
    {
        yield from $this->features;
    }

    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);
        $entity->values = $this->features;
        return $entity;
    }
}
