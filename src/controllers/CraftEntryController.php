<?php

namespace esign\craftcmscrud\controllers;

use Craft;
use craft\elements\Asset;
use stdClass;
use craft\web\Controller;
use craft\elements\Entry as CraftElementEntry;
use craft\helpers\StringHelper;
use craft\records\EntryType as CraftRecordEntryType;
use craft\records\VolumeFolder;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;

class CraftEntryController extends Controller
{
    protected static function getEntry(CraftEntry $model): CraftElementEntry
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
            $entry->enabled = $model->enabledOnCreate;
        }

        $entry->sectionId = $entryType->getAttribute('sectionId');
        $entry->typeId = $entryType->getAttribute('id');
        $entry->fieldLayoutId = $entryType->getAttribute('fieldLayoutId');
        $entry->authorId = getenv('ESING_SYNC_USER') ?? 23;

        return $entry;
    }

    protected static function setFields(CraftElementEntry $entry, stdClass $fields): void
    {
        // Set Craft CMS title & slug
        if (isset($fields->title)) {
            $entry->title = $fields->title;
            unset($fields->title);
        }

        if (isset($fields->slug)) {
            $entry->slug = $fields->slug;
            unset($fields->slug);
        }

        // Set all other fields
        $entry->setFieldValues(json_decode(json_encode($fields), true));
    }

    protected static function saveNestedEntries(CraftElementEntry $entry, array $nestedEntries): void
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
                        $nestedEntry->nestedEntries,
                        $nestedEntry->assets,
                        $nestedEntry->enabledOnCreate
                    ),
                )?->id;
            }
            $entry->setFieldValue($nestedEntry->handle, $nestedEntryIds);
        }
    }

    public static function parseNestedMatrixBlocks(array $nestedEntries, array $matrixHandles): array
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

    protected static function saveMatrixBlocks(CraftElementEntry $entry, array $matrixBlocks): void
    {
        foreach ($matrixBlocks as $block) {
            $blocks = [];
            foreach ($block->fields as $blockFields) {
                $blocks[] = [
                    'type' => $block->handleBlock,
                    // TODO expects to have this toArray() function
                    'fields' => $blockFields->toArray(),
                ];
            }
            $entry->setFieldValue($block->handle, $blocks);
        }
    }

    public static function saveAssets(CraftElementEntry $entry, array $assets): void
    {
        foreach ($assets as $assetField) {
            $filename = $assetField->filename;
            $tempPath = Craft::$app->getPath()->getTempPath();
            $tempFile = $tempPath . '/' . $filename;
            $imageData = file_get_contents($assetField->imageUrl);
            file_put_contents($tempFile, $imageData);

            $folder = VolumeFolder::find()->where(['path' => StringHelper::ensureRight($assetField->path, '/')])->one();
            $asset = Asset::find()->folderId($folder->id)->volumeId($folder->volumeId)->filename($filename)->one() ?? new Asset();
            $asset->avoidFilenameConflicts = true;
            $asset->tempFilePath = $tempFile;
            $asset->filename = $filename;
            $asset->title = $filename;
            $asset->newFolderId = $folder->id;
            $asset->volumeId = $folder->volumeId;
            $asset->uploaderId = getenv('ESING_SYNC_USER') ?? 23;

            if (Craft::$app->elements->saveElement($asset)) {
                $entry->setFieldValue($assetField->handle, [$asset->id]);
            } else {
                throw new \Exception("Couldn't save asset: " . print_r($asset->getErrors(), true));
            }
        }
    }

    public static function updateOrCreateEntry(CraftEntry $model): CraftElementEntry
    {
        $entry = self::getEntry($model);

        if (isset($model->matrixBlocks)) {
            self::saveMatrixBlocks($entry, $model->matrixBlocks);
        }

        if (isset($model->nestedEntries)) {
            self::saveNestedEntries($entry, $model->nestedEntries);
        }

        if (isset($model->assets)) {
            self::saveAssets($entry, $model->assets);
        }

        self::setFields($entry, $model->fields);

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
