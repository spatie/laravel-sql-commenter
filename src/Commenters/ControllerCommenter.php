<?php

namespace Spatie\SqlCommenter\Commenters;

use Closure;
use Illuminate\Database\Connection;
use Laravel\SerializableClosure\Support\ReflectionClosure;
use Spatie\SqlCommenter\Comment;

class ControllerCommenter implements Commenter
{
    public function __construct(protected bool $includeNamespace = false)
    {
    }

    /** @return Comment|array<Comment>|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        if (! request()->route()) {
            return null;
        }

        $action = request()->route()->getAction('uses');

        if ($action instanceof Closure) {
            $reflection = new ReflectionClosure($action);
            $controller = 'Closure';
            $action = $reflection->getFileName();
        } else {
            $controller = $this->includeNamespace
                ? explode('@', $action)[0] ?? null
                : class_basename(explode('@', $action)[0]);

            $action = explode('@', $action)[1] ?? null;
        }

        return [
            new Comment('controller', $controller),
            new Comment('action', $action),
        ];
    }
}
