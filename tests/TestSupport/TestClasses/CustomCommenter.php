<?php

namespace Spatie\SqlCommenter\Tests\TestSupport\TestClasses;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Spatie\SqlCommenter\Comment;
use Spatie\SqlCommenter\SqlCommenter;

class CustomCommenter extends SqlCommenter
{
    protected function addExtraComments(Collection $comments, string $query, Connection $connection): void
    {
        parent::addExtraComments($comments, $query, $connection);

        $comments->push(Comment::make('framework', 'spatie-framework'));
    }
}
