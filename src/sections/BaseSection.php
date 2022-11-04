<?php

namespace modules\module\sections;

use stdClass;
use craft\helpers\StringHelper;
use modules\module\contracts\EntryInterface;
use modules\module\contracts\ResponseInterface;

class BaseSection implements ResponseInterface, EntryInterface
{
    public static function prepareTitleAndSlug(stdClass $object, string $title, ?string $slug = null): stdClass
    {
        $fields = clone $object;
        $fields->title = $title;
        $fields->slug = StringHelper::slugify($title);
        if (! is_null($slug)) {
            $fields->slug = $slug;
        }

        return $fields;
    }
}
