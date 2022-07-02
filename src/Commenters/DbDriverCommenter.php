<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

class DbDriverCommenter implements Commenter
{
    /** @return Comment|Comment[]|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        return Comment::make('db_driver', $connection->getConfig('driver'));
    }
}
