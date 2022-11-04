<?php

namespace modules\module\support\craft;

use stdClass;

class CraftEntry extends stdClass
{
    public string $handle;
    public ?string $identifier;
    public stdClass $fields;
    public ?array $matrixBlocks;
    public ?array $nestedEntries;

    public function __construct(string $handle, ?string $identifier = null, stdClass $fields, ?array $matrixBlocks = null, ?array $nestedEntries = null)
    {
        $this->handle = $handle;
        $this->identifier = $identifier;
        $this->fields = $fields;
        $this->matrixBlocks = $matrixBlocks;
        $this->nestedEntries = $nestedEntries;
    }
}
