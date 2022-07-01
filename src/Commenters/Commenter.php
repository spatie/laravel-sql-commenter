<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

interface Commenter
{
    /** @return Comment|array<Comment> */
    public function comments(string $query, Connection $connection): Comment|array;
}
