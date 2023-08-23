<?php

namespace esign\craftcmscrud\support;

class CraftEntrySettings
{
    public function __construct(
        public bool $enabledOnCreate = true,
        public bool $updateTitleAndSlug = true,
    ) {
    }
}
