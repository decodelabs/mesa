<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa;

use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;

class AliasMap implements Dumpable
{
    /**
     * @var array<string,int>
     */
    protected array $aliases = [];

    /**
     * @param array<string> $aliases
     */
    public function __construct(
        array $aliases
    ) {
        $this->aliases = [];

        foreach (array_values($aliases) as $index => $alias) {
            $this->aliases[$alias] = $index;
        }
    }

    public function get(
        string $alias
    ): ?int {
        if (isset($this->aliases[$alias])) {
            return $this->aliases[$alias];
        }

        if (is_numeric($alias)) {
            return (int)$alias;
        }

        return null;
    }

    public function resolve(
        int $index
    ): ?string {
        $alias = array_search($index, $this->aliases);

        if ($alias === false) {
            return null;
        }

        return $alias;
    }

    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);
        $entity->values = array_flip($this->aliases);
        return $entity;
    }
}
