<?php

namespace esign\craftcmscrud\support;

use stdClass;

class CraftEntry extends stdClass
{
    public function __construct(
        string $handle,
        ?string $identifier = null,
        stdClass|array $fields,
        ?array $matrixBlocks = null,
        ?array $nestedEntries = null,
    ) {
    }
}
