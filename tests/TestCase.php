<?php

namespace Stafe\OptimisticLocking\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Stafe\OptimisticLocking\OptimisticLockingServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Stafe\\OptimisticLocking\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            OptimisticLockingServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__.'/database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }
}
