<?php

namespace modules\module\contracts;

use stdClass;

interface CraftTitleAndSlugInterface
{
    public static function prepareTitleAndSlug(stdClass $object, string $title, ?string $slug = null);
}
