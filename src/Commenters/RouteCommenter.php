<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

class RouteCommenter implements Commenter
{
    /** @return Comment|array<Comment> */
    public function comments(string $query, Connection $connection): Comment|array
    {
        return [
            new Comment('url', request()->getPathInfo()),
            new Comment('route', request()->route()?->getName()),
        ];
    }
}
