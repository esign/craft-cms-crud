<?php

namespace esign\craftcmscrud\controllers;

use Craft;
use stdClass;
use craft\web\Controller;
use craft\elements\Entry as CraftElementEntry;
use craft\records\EntryType as CraftRecordEntryType;
use esign\craftcmscrud\support\CraftEntry;

class EntryHandler
{
    public static function getEntry(CraftEntry $model): CraftElementEntry
    {
        $entryType = CraftRecordEntryType::find()->where(['handle' => $model->handle])->one();
        $entry = null;
        if (!is_null($model->identifier)) {
            $entry = CraftElementEntry::find()
                ->status(CraftElementEntry::statuses())
                ->section($model->handle)
                ->{$model->identifier}($model->fields->{$model->identifier})
                ->one();
        }
        if (is_null($entry)) {
            $entry = new CraftElementEntry();
        }

        $entry->sectionId = $entryType->getAttribute('sectionId');
        $entry->typeId = $entryType->getAttribute('id');
        $entry->fieldLayoutId = $entryType->getAttribute('fieldLayoutId');
        $entry->authorId = getenv('ESING_SYNC_USER') ?? 23;

        return $entry;
    }

    public static function saveNestedEntries(CraftElementEntry $entry, array $nestedEntries): void
    {
        foreach ($nestedEntries as $nestedEntry) {
            $nestedEntryIds = [];
            foreach ($nestedEntry->fields as $nestedEntryFieldValues) {
                $nestedEntryIds[] = CraftHandler::updateOrCreateEntry(
                    new CraftEntry(
                        $nestedEntry->handle,
                        $nestedEntry->identifier,
                        $nestedEntryFieldValues,
                        $nestedEntry->matrixBlocks,
                        $nestedEntry->nestedEntries,
                        $nestedEntry->assets,
                        $nestedEntry->settings,
                    ),
                )?->id;
            }
            $entry->setFieldValue($nestedEntry->handle, $nestedEntryIds);
        }
    }
}
