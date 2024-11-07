# Craft Programmatically CRUD

This module contains the basic CRUD functions for Craft CMS

## Installation

You can install the package via composer, and Craft to install it:

```bash
composer require esign/craft-cms-crud && php craft plugin/install craft-cms-crud 
```

## Usage

### updateOrCreateEntry

Currently there is only one function and that is `updateOrCreateEntry`, this can update an entry with all his fields (fields, matrix blocks & nested entries)

`$entry` has to be an instance of `esign\craftcmscrud\support\CraftEntry` then we are sure all fields can be mapped right

```php
use esign\craftcmscrud\controllers\CraftEntryController;

class YourController extends CraftEntryController
{
    CraftEntryController::updateOrCreateEntry($entry);
}
```

## Entry Objects
---

###  **CraftEntry.php**
```php
use esign\craftcmscrud\support\CraftEntry;

new CraftEntry(
    $handle, 
    $identifier, 
    $fields, 
    $matrixBlocks, 
    $nestedEntries
)
```

`$handle` -> expects your section handle name

`$identifier` -> expects your identifier for that entry (used for updating an entry instead of creating one)

`$fields` -> expects a stdClass of your entry fields (including title & slug) `OR` an array of stdClasses of your entry fields (this is used for nested entries more on this later)

`$matrixBlocks` -> expects an array of CraftMatrixBlock classes (see section CraftMatrixBlock)

`$nestedEntries` -> expects an array of CraftEntry classes



### **CraftMatrixBlock.php**
```php
use esign\craftcmscrud\support\CraftMatrixBlock;

new CraftMatrixBlock(
    $handle, 
    $handleBlock, 
    $fields
)
```

`$handle` -> expects your field handle name

`$handleBlock` -> expects your matrix block handle name

`$fields` -> expects an array of stdClasses of your matrix fields

### **CraftAsset.php**
```php
use esign\craftcmscrud\support\CraftAsset;

new CraftAsset(
    $handle,
    $imageUrl,
    $filename,
    $path,
)
```

`$handle` -> expects your field handle name

`$imageUrl` -> expects your external image url

`$filename` -> expects filename

`$path` -> expects the path of your asset field



## Example
---
```php
use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;

CraftEntryController::updateOrCreateEntry(
    new CraftEntry(
        self::HANDLE_CLUB,
        self::IDENTIFIER_CLUB,
        ClubModel::fieldsFromClub($club),
        [
            new CraftMatrixBlock(
                self::HANDLE_OPENING_HOURS,
                self::HANDLE_OPENING_HOURS_BLOCK,
                $club->{self::HANDLE_OPENING_HOURS}
            ),
            ...
        ],
        [
            new CraftEntry(
                self::HANDLE_CLUB_TAGS,
                self::IDENTIFIER_CLUB_TAGS,
                ClubModel::collectionFieldsFromClubTags($club->{self::HANDLE_CLUB_TAGS})
            ),
            ...
        ],
        [
            new CraftAsset(
                self::HANDLE_IMAGE,
                $contract->mlContractImageUrl,
                StringHelper::beforeFirst(StringHelper::afterLast($contract->mlContractImageUrl, '/'), '?'),
                self::HANDLE_IMAGE_PATH
            )
        ],
    ),
);
```

## parseNestedMatrixBlocks
---
```php
use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;

public const MATRIX_BLOCKS_CONTRACT_TERM = [
    'mlTermPriceAdjustmentRules' => 'mlPriceBlock',
    'mlTermFlatFees' => 'mlFeeBlock',
    'mlTermOptionalModules' => 'mlOptionalBlock',
    'mlTermRateBonusPeriods' => 'mlBonusBlock',
];

CraftEntryController::updateOrCreateEntry(
    new CraftEntry(
        self::HANDLE_CONTRACT,
        self::IDENTIFIER_CONTRACT,
        Entry::fieldsFromContract($contract),
        null,
        [
            new CraftEntry(
                self::HANDLE_CONTRACT_TERM,
                self::IDENTIFIER_CONTRACT_TERM,
                Entry::collectionFieldsFromContractTerms(
                    $contract->{self::HANDLE_CONTRACT_TERM}
                ),
                CraftEntryController::parseNestedMatrixBlocks(
                    $contract->{self::HANDLE_CONTRACT_TERM},
                    self::MATRIX_BLOCKS_CONTRACT_TERM
                ),
            ),
        ],
    ),
);
```

`CraftEntryController::parseNestedMatrixBlocks()` is used to parse the nested matrix blocks. 

`MATRIX_BLOCKS_CONTRACT_TERM` is the $sectionHandle => $blockHandle