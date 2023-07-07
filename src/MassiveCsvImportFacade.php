<?php

namespace Ascentech\MassiveCsvImport;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ascentech\MassiveCsvImport\Skeleton\SkeletonClass
 */
class MassiveCsvImportFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'massive-csv-import';
    }
}
