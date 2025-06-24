<?php

namespace Stafe\OptimisticLocking;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class StaleModelException extends RuntimeException
{
    protected array $diff;

    public function __construct(Model $model, $current)
    {
        parent::__construct('Stale model detected.');

        $column = config('optimistic.column', 'lock_version');
        $maxLen = (int) config('optimistic.diff_max_len', 250);

        $changes = $model->getDirty();
        unset($changes[$column]);

        $original = $model->newQuery()->whereKey($model->getKey())->first();
        $diff = [];
        foreach ($changes as $key => $value) {
            $old = data_get($original, $key);
            $new = $value;
            if (is_string($old)) {
                $old = mb_strimwidth($old, 0, $maxLen, '...');
            }
            if (is_string($new)) {
                $new = mb_strimwidth($new, 0, $maxLen, '...');
            }
            $diff[$key] = ['old' => $old, 'new' => $new];
        }
        $this->diff = $diff;
    }

    public function diff(): array
    {
        return $this->diff;
    }
}
