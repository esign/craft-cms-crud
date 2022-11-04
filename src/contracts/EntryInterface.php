<?php

namespace modules\module\contracts;

use stdClass;
use modules\module\support\objects\BaseObject;

interface EntryInterface
{
    public static function prepareTitleAndSlug(stdClass $object, string $title, ?string $slug = null);
    
    public static function getEntryFields(BaseObject $object);
}
