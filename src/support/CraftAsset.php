<?php

namespace esign\craftcmscrud\support;

class CraftAsset
{
    public function __construct(
        public string $handle,
        public string $imageUrl,
        public string $filename,
        public string $path,
    ) {
    }
}
