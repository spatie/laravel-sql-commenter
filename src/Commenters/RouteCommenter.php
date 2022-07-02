<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

class RouteCommenter implements Commenter
{
    /** @return Comment|array<Comment>|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        return [
            Comment::make('url', request()->getPathInfo()),
            Comment::make('route', request()->route()?->getName()),
        ];
    }
}
