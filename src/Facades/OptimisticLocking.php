<?php

namespace Stafe\OptimisticLocking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \VendorName\Skeleton\Skeleton
 */
class OptimisticLocking extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Stafe\OptimisticLocking\OptimisticLocking::class;
    }
}
