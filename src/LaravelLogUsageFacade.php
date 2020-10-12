<?php

namespace MapIr\LaravelLogUsage;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MapIr\LaravelLogUsage\Skeleton\SkeletonClass
 */
class LaravelLogUsageFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-log-usage';
    }
}
