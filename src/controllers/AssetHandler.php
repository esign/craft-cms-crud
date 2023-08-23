<?php

namespace esign\craftcmscrud\controllers;

use Craft;
use craft\elements\Entry as CraftElementEntry;
use craft\records\VolumeFolder;
use craft\helpers\StringHelper;
use craft\elements\Asset;

class AssetHandler
{
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
}
