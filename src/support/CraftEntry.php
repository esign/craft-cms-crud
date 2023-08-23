<?php

namespace esign\craftcmscrud\support;

use stdClass;
use craft\helpers\StringHelper;
use craft\elements\Entry as CraftElementEntry;
use esign\craftcmscrud\controllers\EntrySettingsHandler;

class CraftEntry
{
    public function __construct(
        public string $handle,
        public ?string $identifier = null,
        public stdClass|array $fields,
        public ?array $matrixBlocks = null,
        public ?array $nestedEntries = null,
        public ?array $assets = null,
        public ?CraftEntrySettings $settings = null
    ) {
        if ($settings === null) {
            $this->settings = new CraftEntrySettings();
        }
    }

    public function setTitle(string $title): void
    {
        // if it's an array its a collection of entries
        if (is_array($this->fields)) {
            foreach ($this->fields as &$entry) {
                $entry->title = $title;
            }
        } elseif (is_object($this->fields)) {
            $this->fields->title = $title;
        }
    }

    public function setSlug(string $slug): void
    {
        if (is_array($this->fields)) {
            foreach ($this->fields as &$entry) {
                $entry->slug = StringHelper::slugify($slug);
            }
        } elseif (is_object($this->fields)) {
            $this->fields->slug = StringHelper::slugify($slug);
        }
    }
}
