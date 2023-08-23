<?php

namespace esign\craftcmscrud\controllers;

use craft\elements\Entry as CraftElementEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;

class MatrixBlockHandler
{
    public static function saveMatrixBlocks(CraftElementEntry $entry, array $matrixBlocks): void
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
}
