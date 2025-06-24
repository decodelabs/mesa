<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa\Writer;

class FeatureRegister
{
    /**
     * @var array<string,bool>
     */
    protected(set) array $features = [];

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
        string $feature
    ): bool {
        return $this->features[$feature] ?? true;
    }
}
