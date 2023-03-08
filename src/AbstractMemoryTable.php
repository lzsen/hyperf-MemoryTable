<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Lzsen\MemoryTable;

use Hyperf\Memory\TableManager;
use Swoole\Table;

/**
 * @method bool set(string $key, array $value)
 * @method int incr(string $key, string $column, mixed $incrby = 1)
 * @method int decr(string $key, string $column, mixed $decrby = 1)
 * @method array|false get(string $key, string $field = null)
 * @method bool exist(string $key)
 * @method int count()
 * @method bool del(string $key)
 * @method array stats()
 * @method int getMemorySize()
 * @method int getSize()
 */
abstract class AbstractMemoryTable
{
    public const TYPE_INT = Table::TYPE_INT;

    public const TYPE_FLOAT = Table::TYPE_FLOAT;

    public const TYPE_STRING = Table::TYPE_STRING;

    protected string $table = '';

    protected int $size = 1000;

    protected float $conflictProportion = 0.2;

    protected array $fields = [];

    public function __call(string $name, array $arguments)
    {
        if (! TableManager::has($this->table)) {
            throw new \RuntimeException(sprintf('Memory table %s is not defined', $this->table));
        }
        try {
            $table = TableManager::get($this->table);
            return $table->{$name}(...$arguments);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Method %s does not exist', $this->table));
        }
    }

    public function getTable(): string
    {
        return $this->table;
    }

    // initialization
    protected function initialization(): void
    {
        if (empty($this->table)) {
            $this->table = strtr(get_called_class(), ['\\' => '_']);
        }
        if (! empty($this->fields)) {
            $table = TableManager::initialize($this->table, $this->size, $this->conflictProportion);
            foreach ($this->fields as $field) {
                $table->column(...$field);
            }
            $table->create();
        }
    }
}
