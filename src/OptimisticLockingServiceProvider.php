<?php

namespace Stafe\OptimisticLocking;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stafe\OptimisticLocking\Commands\LockCommand;

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
}
