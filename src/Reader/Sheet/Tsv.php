<?php

/**
 * @package Mesa
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Mesa\Reader\Sheet;

class Tsv extends Csv
{
    protected const string Separator = "\t";
}
