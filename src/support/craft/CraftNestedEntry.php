<?php

namespace modules\module\support\craft;

use stdClass;

class CraftNestedEntry extends stdClass
{
    public string $handle;
    public ?string $identifier;
    public array $fields;
    public ?array $matrixBlocks;
    public ?array $nestedEntries;

    public function __construct(string $handle, ?string $identifier = null, array $collectionFields, ?array $matrixBlocks = null, ?array $nestedEntries = null)
    {
        $this->handle = $handle;
        $this->identifier = $identifier;
        $this->fields = $collectionFields;
        $this->matrixBlocks = $matrixBlocks;
        $this->nestedEntries = $nestedEntries;
    }
}
