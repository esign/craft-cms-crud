<?php

namespace modules\module\controllers;

use Craft;
use modules\module\controllers\CraftEntryController;
use modules\module\sections\example\Example;
use modules\module\support\craft\CraftEntry;
use modules\module\support\craft\CraftMatrixBlock;
use modules\module\support\craft\CraftNestedEntry;

class ExampleController extends CraftEntryController
{
    public const ENTRY_HANDLE = 'handleName';
    public const ENTRY_IDENTIFIER = 'id';

    public const MATRIX_HANDLE = 'handleName';
    public const MATRIX_BLOCK_HANDLE = 'handleName';

    public const NESTED_ENTRY_HANDLE = 'handleName';
    public const NESTED_ENTRY_IDENTIFIER = 'id';

    // parseNestedMatrixBlocks is just a function that returns an array of CraftMatrixBlock
    public const ARRAY_OF_MATRIXES = [
        'matrixHandleName1' => 'blockHandleName1',
        'matrixHandleName2' => 'blockHandleName2',
        // ...
    ];

    public function sync()
    {
        $data = Example::fromResponse('fetch here your data');

        foreach ($data as $exampleData) {
            $this->updateOrCreateEntry(
                new CraftEntry(
                    self::ENTRY_HANDLE,
                    self::ENTRY_IDENTIFIER,
                    Example::getEntryFields($exampleData),
                    [
                        new CraftMatrixBlock(
                            self::MATRIX_HANDLE,
                            self::MATRIX_BLOCK_HANDLE,
                            $exampleData->{self::MATRIX_HANDLE}
                        ),
                    ],
                    [
                        new CraftNestedEntry(
                            self::NESTED_ENTRY_HANDLE,
                            self::NESTED_ENTRY_IDENTIFIER,
                            Example::getNestedEntriesFields($exampleData->{self::NESTED_ENTRY_HANDLE}),
                            $this->parseNestedMatrixBlocks($exampleData->{self::NESTED_ENTRY_HANDLE}, self::ARRAY_OF_MATRIXES),
                        ),
                    ],
                ),
            );
        }
    }
}
