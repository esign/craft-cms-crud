<?php

namespace esign\craftcmscrud\support;

use stdClass;

class CraftAsset extends stdClass
{
    public function __construct(
        public string $handle,
        public string $imageUrl,
        public string $filename,
        public string $path,
    ) {
    }
}
