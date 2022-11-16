<?php

namespace esign\craftCmsCrud\support;

use stdClass;

class CraftMatrixBlock extends stdClass
{
    public function __construct(
        public string $handle,
        public string $handleBlock,
        public array $fields
    ) {
    }
}
