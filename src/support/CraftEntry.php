<?php

namespace esign\craftcmscrud\support;

use stdClass;

class CraftEntry extends stdClass
{
    public function __construct(
        public string $handle,
        public ?string $identifier = null,
        public stdClass|array $fields,
        public ?array $matrixBlocks = null,
        public ?array $nestedEntries = null,
        public ?array $assets = null,
    ) {
    }
}
