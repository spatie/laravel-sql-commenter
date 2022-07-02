<?php

namespace Spatie\SqlCommenter\Exceptions;

use Exception;
use Spatie\SqlCommenter\SqlCommenter;

class InvalidSqlCommenter extends Exception
{
    public static function make(string $invalidClass): self
    {
        $baseClass = SqlCommenter::class;

        return new self("Class `{$invalidClass}` is not a valid SqlCommenter class. A valid SqlCommenter class is or extends `{$baseClass}`");
    }
}
