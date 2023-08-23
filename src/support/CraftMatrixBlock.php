<?php

namespace esign\craftcmscrud\support;

class CraftMatrixBlock
{
    public function __construct(
        public string $handle,
        public string $handleBlock,
        public array $fields
    ) {
    }
}
