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

    /** @return Comment|Comment[]|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        if (! request()->route()) {
            return null;
        }

        [$controller, $action] = $this->getControllerAndAction();

        return [
            Comment::make('controller', $controller),
            Comment::make('action', $action),
        ];
    }

    /**
     * @return array{
     *     0: class-string,
     *     1: string,
     * }
     */
    protected function getControllerAndAction(): array
    {
        $action = request()->route()->getAction('uses');

        if ($action instanceof Closure) {
            $reflection = new ReflectionClosure($action);
            $controller = 'Closure';
            $action = $reflection->getFileName();

            return [$controller, $action];
        }

        $controller = explode('@', $action)[0] ?? null;

        if (! $this->includeNamespace) {
            $controller = class_basename($controller);
        }

        $action = explode('@', $action)[1] ?? null;

        return [$controller, $action];
    }
}
