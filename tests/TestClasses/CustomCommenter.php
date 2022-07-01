<?php

namespace Spatie\SqlCommenter\Tests\TestClasses;

use Spatie\SqlCommenter\SqlCommenter;

class CustomCommenter extends SqlCommenter
{
    protected static function addFrameworkVersion(): void
    {
        static::addComment('framework', "spatie-framework");
    }
}
