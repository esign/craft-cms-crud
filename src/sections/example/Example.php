<?php

namespace modules\module\sections\example;

use modules\module\support\objects\ExampleObject;
use modules\module\contracts\NestedEntryInterface;
use modules\module\sections\BaseSection;

class Example extends BaseSection implements NestedEntryInterface
{
    public static function fromResponse($response): array
    {
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($example) {
                return new ExampleObject($example);
            }, $responseArray);

        return $mappedData;
    }

    public static function getEntryFields($object): ExampleObject
    {
        $fields = self::prepareTitleAndSlug($object, $object->original->title);
        
        // unset matrix & nested entries variables if necessary
        // unset($fields->matrix);
        // unset($fields->nestedEntries);

        return $fields;
    }

    public static function getNestedEntriesFields($entries): array
    {
        $fields = array_map(function($entry) {
            $fields = self::prepareTitleAndSlug($entry, $entry->original->title);

            // unset matrix & nested entries variables if necessary
            // unset($fields->matrix);
            // unset($fields->nestedEntries);
            
            return $fields;
        }, $entries);

        return $fields;
    }
}
