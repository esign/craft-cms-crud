# Craft Programmatically CRUD Template

This module contains the basic CRU(D) functions for Craft CMS

## Installation

1. Clone this project

2. Renaming: 
    1. Find & replace ``\module`` to your disired module name ``\likeso``
    2. Rename src folder to your module name
    3. Rename ``ExampleModule.php`` to your module name & on line 40 replace ``{module}`` to your module name

3. Copy the folder to your craft modules folder

4. In ``CraftEntryController.php`` set variable const ``AUTHOR_ID`` to your sync user id or esign user id

5. In your craft ``app.php`` add your module
```
    'modules' => [
        ...
        'likeso' => [
            'class' => \modules\likeso\LikeSo::class,
        ]
    ],
    'bootstrap' => [
        ...
        'likeso'
    ],
```

## Examples

``ExampleController.php``

``Example.php``

``ExampleObject.php``
