<?php

namespace Spatie\SqlCommenter\Tests\TestSupport\TestClasses;

use Illuminate\Support\Collection;
use Spatie\SqlCommenter\Comment;
use Spatie\SqlCommenter\SqlCommenter;

class CustomCommenter extends SqlCommenter
{
    protected function addExtraComments(Collection $comments): void
    {
        parent::addExtraComments($comments);

        $comments->push(Comment::make('framework', 'spatie-framework'));
    }
}
