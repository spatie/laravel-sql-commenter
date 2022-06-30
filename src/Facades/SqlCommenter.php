<?php

namespace Spatie\SqlCommenter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\SqlCommenter\SqlCommenter
 */
class SqlCommenter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-sqlcommenter';
    }
}
