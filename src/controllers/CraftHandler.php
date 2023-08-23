<?php

namespace esign\craftcmscrud\controllers;

use Craft;
use craft\elements\Entry as CraftElementEntry;
use esign\craftcmscrud\support\CraftEntry;

class CraftHandler
{
    public static function updateOrCreateEntry(CraftEntry $model): CraftElementEntry
    {
        $entry = EntryHandler::getEntry($model);

        if (isset($model->matrixBlocks)) {
            MatrixBlockHandler::saveMatrixBlocks($entry, $model->matrixBlocks);
        }

        if (isset($model->nestedEntries)) {
            EntryHandler::saveNestedEntries($entry, $model->nestedEntries);
        }

        if (isset($model->assets)) {
            AssetHandler::saveAssets($entry, $model->assets);
        }

        // TODO was hiermee bezig
        EntrySettingsHandler::applySettings($entry, $model);

        $entry->setFieldValues(json_decode(json_encode($model->fields), true));

        if (Craft::$app->elements->saveElement($entry)) {
            return $entry;
        } else {
            throw new \Exception("Couldn't save new entry: " . print_r($entry->getErrors(), true));
        }
    }

    public static function disableEntry(CraftElementEntry $entry): CraftElementEntry
    {
        $entry->enabled = false;
        if (Craft::$app->elements->saveElement($entry)) {
            return $entry;
        } else {
            throw new \Exception("Couldn't save new entry: " . print_r($entry->getErrors(), true));
        }
    }

    public static function disableEntriesExcept(string $sectionHandle, string $databaseColumnName, array $idsToExclude): void
    {
        $entries = CraftElementEntry::find()
            ->section($sectionHandle)
            ->where(['NOT',["content.$databaseColumnName" => $idsToExclude]])
            ->all();

        foreach ($entries as $entry) {
            self::disableEntry($entry);
        }
    }
}
