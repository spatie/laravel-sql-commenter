<?php

namespace Spatie\SqlCommenter;

use Closure;
use Illuminate\Bus\Dispatcher;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use Laravel\SerializableClosure\Support\ReflectionClosure;
use Spatie\Backtrace\Backtrace;
use Spatie\Backtrace\Frame;

class SqlCommenter
{
    private static array $comments = [];

    public static function addComment(string $key, string $value): void
    {
        self::$comments[$key] = $value;
    }

    public static function commentQuery(string $query, Connection $connection): string
    {
        if (str_contains($query, '/*')) {
            return $query;
        }

        self::addFrameworkVersion();
        self::addControllerInfo();
        self::addRouteInfo();
        self::addJobInfo();
        self::addDatabaseDriver($connection);
        self::addFile();

        $comments = array_filter(self::$comments);

        $query = Str::finish(trim($query), ';');

        if (Str::endsWith($query, ';')) {
            return rtrim($query, ";") . self::formatComments($comments). ';';
        }

        return $query . self::formatComments($comments);
    }

    public static function formatComments(array $comments): string
    {
        if (empty($comments)) {
            return '';
        }

        return str(collect($comments)
            ->map(fn ($value, $key) => self::formatComment($key, $value))
            ->join("',"))->prepend('/*')->append("'*/");
    }

    public static function formatComment(string $key, string $value): string
    {
        return urlencode($key) . "=" . "'" . urlencode($value);
    }

    private static function addFrameworkVersion(): void
    {
        if (config('sql-commenter.framework')) {
            self::addComment('framework', "laravel-" . app()->version());
        }
    }

    private static function addControllerInfo(): void
    {
        if (! config('sql-commenter.controller')) {
            return;
        }

        if (! request()->route()) {
            return;
        }

        $action = request()->route()->getAction('uses');

        if ($action instanceof Closure) {
            $reflection = new ReflectionClosure($action);
            $controller = 'Closure';
            $action = $reflection->getFileName();
        } else {
            $controller = config('sql-commenter.controller_namespace')
                ? explode('@', $action)[0] ?? null
                : class_basename(explode('@', $action)[0]);

            $action = explode('@', $action)[1] ?? null;
        }

        self::addComment('controller', $controller);
        self::addComment('action', $action);
    }

    private static function addRouteInfo(): void
    {
        if (! config('sql-commenter.route')) {
            return;
        }

        self::addComment('url', request()->getPathInfo());
        self::addComment('route', request()->route()?->getName());
    }

    private static function addJobInfo(): void
    {
        if (! config('sql-commenter.job')) {
            return;
        }

        if (! app()->runningInConsole()) {
            return;
        }

        /** @phpstan-ignore-next-line */
        $pipeline = invade(app(Dispatcher::class))->pipeline;
        /** @phpstan-ignore-next-line */
        $job = invade($pipeline)->passable;

        $job = config('sql-commenter.job_namespace')
            ? $job::class
            : class_basename($job);

        self::addComment('job', $job);
    }

    private static function addDatabaseDriver(Connection $connection): void
    {
        if (! config('sql-commenter.driver')) {
            return;
        }

        self::addComment('db_driver', $connection->getConfig('driver'));
    }

    private static function addFile(): void
    {
        if (! config('sql-commenter.file')) {
            return;
        }

        $backtrace = new Backtrace();
        $frame = collect($backtrace->frames())
            ->first(function (Frame $frame) {
                return ! str_contains($frame->file, 'laravel/framework')
                    && ! str_contains($frame->file, 'laravel-sql-commenter/src');
            });

        self::addComment('file', $frame?->file);
        self::addComment('line', $frame?->lineNumber);
    }
}
