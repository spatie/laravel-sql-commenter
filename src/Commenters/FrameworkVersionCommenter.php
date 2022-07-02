<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

class FrameworkVersionCommenter implements Commenter
{
    /** @return Comment|Comment[]|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        return Comment::make('framework',  "laravel-" . app()->version());
    }
}
