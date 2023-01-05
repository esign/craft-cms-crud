# Craft Programmatically CRUD

This module contains the basic CRU(D) functions for Craft CMS

## Installation

[WIP] Just copy paste the esign folder in your craft project under ``modules/``

## Usage

### Controller

```
use modules\esign\controllers\CraftEntryController;

class YourController extends CraftEntryController
{
    ...
}
```

## Entry Objects

###  **CraftEntry.php**
```
use modules\esign\support\CraftEntry;

new CraftEntry(
    $handle, 
    $identifier, 
    $fields, 
    $matrixBlocks, 
    $nestedEntries
)
```

``$handle`` -> expects your section handle name

``$identifier`` -> expects your identifier for that entry (used for updating an entry instead of creating one)

``$fields`` -> expects a stdClass of your entry fields (including title & slug) ``OR`` an array of stdClasses of your entry fields (this is used for nested entries more on this later)

``$matrixBlocks`` -> expects an array of CraftMatrixBlock classes (see section CraftMatrixBlock)

``$nestedEntries`` -> expects an array of CraftEntry classes

---

### **CraftMatrixBlock.php**
```
use modules\esign\support\CraftMatrixBlock;

new CraftMatrixBlock(
    $handle, 
    $handleBlock, 
    $fields
)
```

``$handle`` -> expects your field handle name

``$handleBlock`` -> expects your matrix block handle name

``$fields`` -> expects an array of stdClasses of your matrix fields

---
## Example
```
$this->updateOrCreateEntry(
    new Entry(
        self::HANDLE_CLUB,
        self::IDENTIFIER_CLUB,
        ClubModel::fieldsFromClub($club),
        [
            new MatrixBlock(
                self::HANDLE_OPENING_HOURS,
                self::HANDLE_OPENING_HOURS_BLOCK,
                $club->{self::HANDLE_OPENING_HOURS}
            ),
            ...
        ],
        [
            new Entry(
                self::HANDLE_CLUB_TAGS,
                self::IDENTIFIER_CLUB_TAGS,
                ClubModel::collectionFieldsFromClubTags($club->{self::HANDLE_CLUB_TAGS})
            ),
            ...
        ]
    ),
);
```
