<?php

namespace modules\module\contracts;

interface NestedEntryInterface
{
    public static function getNestedEntriesFields(array $entries): array;
}
