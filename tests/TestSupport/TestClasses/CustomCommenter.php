<?php

namespace Spatie\SqlCommenter\Tests\TestSupport\TestClasses;

use Spatie\SqlCommenter\SqlCommenter;

class CustomCommenter extends SqlCommenter
{
    protected function getCommenters(array $commenters): array
    {
        static::addComment('framework', "spatie-framework");

        return $commenters;
    }
}
