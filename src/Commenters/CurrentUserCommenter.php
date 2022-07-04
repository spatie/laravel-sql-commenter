<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

class CurrentUserCommenter implements Commenter
{
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return [
            Comment::make('user_id', $user->getKey()),
            Comment::make('user_email', $user->email),
        ];
    }
}
