<?php

namespace modules\module\support\objects;

use stdClass;
use modules\module\support\objects\BaseObject;

class ExampleObject extends BaseObject
{
    public int $id;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->id = $this->original->id;

        return $this;
    }
}
