<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Bus\Dispatcher;
use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;
use Throwable;

class JobCommenter implements Commenter
{
    public function __construct(protected bool $includeNamespace = false)
    {
    }

    /** @return Comment|Comment[]|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        if (! app()->runningInConsole()) {
            return null;
        }

        try {
            /** @phpstan-ignore-next-line  */
            $pipeline = invade(app(Dispatcher::class))->pipeline;

            /** @phpstan-ignore-next-line  */
            $job = invade($pipeline)->passable;
        } catch (Throwable) {
            return null;
        }

        if ($job) {
            $job = $job::class;

            if (! $this->includeNamespace) {
                $job = class_basename($job);
            }
        }

        return Comment::make('job', $job);
    }
}
