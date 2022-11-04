<?php

namespace modules\module\support\craft;

use stdClass;

class CraftMatrixBlock extends stdClass
{
    public string $handle;
    public string $handleBlock;
    public array $fields;

    public function __construct(string $handle, string $handleBlock, array $fields)
    {
        $this->handle = $handle;
        $this->handleBlock = $handleBlock;
        $this->fields = $fields;
    }
}
