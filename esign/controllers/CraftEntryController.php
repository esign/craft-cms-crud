<?php

namespace modules\esign\controllers;

use Craft;
use stdClass;
use craft\web\Controller;
use craft\elements\Entry as CraftElementEntry;
use craft\records\EntryType as CraftRecordEntryType;
use modules\esign\support\CraftEntry;
use modules\esign\support\CraftMatrixBlock;

class CraftEntryController extends Controller
{
    public const SYNC_USER_ID = 'sync_user_id';

    public static function setFields(CraftElementEntry $model, stdClass $fields): CraftElementEntry
    {
        if(isset($fields->title)) {
            $model->title = $fields->title;
            unset($fields->title);
        }

        if(isset($fields->slug)) {
            $model->slug = $fields->slug;
            unset($fields->slug);
        }

        $model->setFieldValues(json_decode(json_encode($fields), true));

        return $model;
    }

    protected static function updateOrCreateEntry(CraftEntry $model): CraftElementEntry
    {
        $entryType = CraftRecordEntryType::find()->where(['handle' => $model->handle])->one();
        $entry = null;
        if (!is_null($model->identifier)) {
            $entry = CraftElementEntry::find()->section($model->handle)->{$model->identifier}($model->fields->{$model->identifier})->one();
        }
        if (is_null($entry)) {
            $entry = new CraftElementEntry();
        }

        $entry->sectionId = $entryType->getAttribute('sectionId');
        $entry->typeId = $entryType->getAttribute('id');
        $entry->fieldLayoutId = $entryType->getAttribute('fieldLayoutId');
        $entry->authorId = self::SYNC_USER_ID;

        if (isset($model->matrixBlocks)) {
            $entry = self::saveMatrixBlocks($entry, $model->matrixBlocks);
        }

        if (isset($model->nestedEntries)) {
            $entry = self::saveNestedEntries($entry, $model->nestedEntries);
        }

        $entry = self::setFields($entry, $model->fields);

        if(Craft::$app->elements->saveElement($entry)) {
            return $entry;
        } else {
            throw new \Exception("Couldn't save new entry: " . print_r($entry->getErrors(), true));
        }
    }

    protected static function saveMatrixBlocks(CraftElementEntry $entry, array $matrixBlocks): CraftElementEntry
    {
        $blocks = [];
        foreach ($matrixBlocks as $block) {
            $matrixHandle = $block->handle;
            foreach ($block->fields as $key => $blockFields) {
                $blocks[] = [
                    'type' => $block->handleBlock,
                    'fields' => [
                        ...$blockFields->toArray()
                    ],
                ];
            }
            $entry->setFieldValue($matrixHandle, $blocks);
        }

        return $entry;
    }

    protected static function saveNestedEntries(CraftElementEntry $entry, array $nestedEntries): CraftElementEntry
    {
        foreach ($nestedEntries as $nestedEntry) {
            $nestedEntryIds = [];
            foreach ($nestedEntry->fields as $nestedEntryFieldValues) {
                $nestedEntryIds[] = self::updateOrCreateEntry(
                    new CraftEntry(
                        $nestedEntry->handle,
                        $nestedEntry->identifier,
                        $nestedEntryFieldValues,
                        $nestedEntry->matrixBlocks,
                        $nestedEntry->nestedEntries
                    ),
                )?->id;
            }
            $entry->setFieldValue($nestedEntry->handle, $nestedEntryIds);
        }

        return $entry;
    }

    protected function parseNestedMatrixBlocks(array $nestedEntries, array $matrixHandles): array
    {
        // first loop over the nested entries to get the matrix block of that entry
        $blocks = [];
        foreach ($nestedEntries as $key => $value) {
            foreach ($matrixHandles as $sectionHandle => $blockHandle) {
                $blocks[] = new CraftMatrixBlock(
                    $sectionHandle,
                    $blockHandle,
                    $nestedEntries[$key]->{$sectionHandle}
                );
            }
        }

        return $blocks;
    }
}
