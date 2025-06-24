<?php

namespace Stafe\OptimisticLocking;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stafe\OptimisticLocking\Commands\LockCommand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OptimisticLockingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('optimistic-locking')
            ->hasConfigFile('optimistic')
            ->hasCommand(LockCommand::class);
    }

    public function packageBooted(): void
    {
        Builder::macro('updateAndIncrementLock', function (array $values) {
            $model = $this->getModel();

            if (method_exists($model, 'getOptimisticLockColumn')) {
                $column = $model->getOptimisticLockColumn();
                $values[$column] = DB::raw("{$column} + 1");
            }

            return $this->update($values);
        });
    }
}
