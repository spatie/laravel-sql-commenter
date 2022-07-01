<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

class DbDriverCommenter implements Commenter
{
    /** @return Comment|array<Comment> */
    public function comments(string $query, Connection $connection): Comment|array
    {
        return new Comment('db_driver', $connection->getConfig('driver'));
    }
}
