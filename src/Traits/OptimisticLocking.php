<?php

namespace Stafe\OptimisticLocking\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Stafe\OptimisticLocking\StaleModelException;

trait OptimisticLocking
{
    public static function bootOptimisticLocking(): void
    {
        static::creating(function (Model $model) {
            $column = $model->getOptimisticLockColumn();
            if (! $model->isDirty($column)) {
                $model->{$column} = config('optimistic.start_value', 1);
            }
        });

        static::updating(function (Model $model) {
            $column = $model->getOptimisticLockColumn();
            $model->{$column} = ($model->getOriginal($column) ?? 0) + 1;
        });
    }

    public function getOptimisticLockColumn(): string
    {
        return config('optimistic.column', 'lock_version');
    }

    protected function performUpdate(Builder $query)
    {
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }

        $column = $this->getOptimisticLockColumn();
        $current = $this->getOriginal($column);
        $query->where($column, $current);

        $dirty = $this->getDirtyForUpdate();
        $dirty[$column] = $this->{$column};

        if (count($dirty) > 0) {
            $updated = $this->setKeysForSaveQuery($query)->update($dirty);

            if ($updated === 0) {
                throw new StaleModelException($this, $current);
            }

            $this->syncChanges();

            $this->fireModelEvent('updated', false);
        }

        return true;
    }
}
