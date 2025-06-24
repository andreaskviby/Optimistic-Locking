<?php

namespace Stafe\OptimisticLocking\Commands;

use Illuminate\Console\Command;

class LockCommand extends Command
{
    public $signature = 'schema:lock {--apply}';

    public $description = 'Manage optimistic lock columns';

    public function handle(): int
    {
        $column = config('optimistic.column', 'lock_version');
        if ($this->option('apply')) {
            foreach (\File::allFiles(app_path('Models')) as $file) {
                $class = 'App\\Models\\'.pathinfo($file, PATHINFO_FILENAME);
                if (is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class)) {
                    if (! \Schema::hasColumn((new $class)->getTable(), $column)) {
                        \Schema::table((new $class)->getTable(), function ($table) use ($column) {
                            $table->unsignedInteger($column)->nullable();
                        });
                        $this->info("Added {$column} column to ".(new $class)->getTable());
                    }
                }
            }

            return self::SUCCESS;
        }
        $this->comment('No action');

        return self::SUCCESS;
    }
}
