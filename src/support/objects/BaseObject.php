<?php

namespace modules\module\support\objects;

use stdClass;
use modules\module\contracts\BaseOjectInterface;

abstract class BaseObject extends stdClass implements BaseOjectInterface
{
    public function __construct(
        protected stdClass $original
    ) {}

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }
}
