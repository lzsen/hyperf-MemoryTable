#   

```
composer require lzsen/hyperf-memory-table
```

~~~ php

<?php
namespace App\MemoryTable;

use Lzsen\MemoryTable\AbstractMemoryTable;
use Lzsen\MemoryTable\Annotation\MemoryTable;

#[MemoryTable]
class TestTable extends AbstractMemoryTable
{
    protected string $table = 'fd'; 
    protected int $size = 100;

    // 字段
    protected array $fields = [
        [
            'name' => 'f1',
            'type' => AbstractMemoryTable::TYPE_STRING,
            'size' => 10,
        ],
    ];
}
~~~