<?php

namespace esign\craftcmscrud\controllers;

use craft\elements\Entry as CraftElementEntry;
use esign\craftcmscrud\support\CraftEntrySettings;
use stdClass;

class EntrySettingsHandler
{
    public static function applySettings(CraftElementEntry $entry, CraftEntry $model): void
    {
        self::applyTitleAndSlug($entry, $model->fields, $model->settings);
        self::applyEnabledOnCreate($entry, $model->settings);
    }

    public static function applyTitleAndSlug(CraftElementEntry $entry, stdClass $fields, CraftEntrySettings $settings): void
    {
        if (is_null($entry->id) || $settings->updateTitleAndSlug) {
            if (isset($fields->title)) {
                $entry->title = $fields->title;
            }

            if (isset($fields->slug)) {
                $entry->slug = $fields->slug;
            }
        }
    }

    public static function applyEnabledOnCreate(CraftElementEntry $entry, CraftEntrySettings $settings): void
    {
        if (is_null($entry->id) && !is_null($settings->enabledOnCreate)) {
            $entry->enabled = $settings->enabledOnCreate;
        }
    }
}
