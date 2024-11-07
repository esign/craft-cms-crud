<?php

namespace esign\craftcmscrud\interfaces;

use esign\craftcmscrud\support\CraftEntry;

interface EntryInterface
{
    public static function getEntry($data): CraftEntry;
    public static function getMatrixBlocks($data): ?array;
    public static function getNestedEntries($data): ?array;
    public static function getAssets($data): ?array;
}
