<?php

namespace Spatie\SqlCommenter;

use Closure;
use Illuminate\Bus\Dispatcher;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use Laravel\SerializableClosure\Support\ReflectionClosure;
use Spatie\Backtrace\Backtrace;

class SqlCommenter
{
    private static $tags = [];

    public static function addTag(string $key, string $value): void
    {
        self::$tags[$key] = $value;
    }

    public static function commentQuery(string $query, Connection $connection): string
    {
        $comment = [];

        if (config('sql-commenter.framework')) {
            $comment['framework'] = "laravel-" . app()->version();
        }

        if (request()->route() && config('sql-commenter.controller')) {
            $action = request()->route()->getAction('uses');

            if ($action instanceof Closure) {
                $reflection = new ReflectionClosure($action);
                $comment['controller'] = 'Closure';
                $comment['action'] = $reflection->getFileName();
            } else {
                $comment['controller'] = config('sql-commenter.controller_namespace')
                    ? explode('@', $action)[0] ?? null
                    : class_basename(explode('@', $action)[0]);

                $comment['action'] = explode('@', $action)[1] ?? null;
            }
        }

        if (config('sql-commenter.route')) {
            $comment['url'] = request()->getPathInfo();
            $comment['route'] = request()->route()?->getName();
        }

        if (app()->runningInConsole() && config('sql-commenter.job')) {
            /** @phpstan-ignore-next-line */
            $pipeline = invade(app(Dispatcher::class))->pipeline;
            /** @phpstan-ignore-next-line */
            $job = invade($pipeline)->passable;

            $comment['job'] = config('sql-commenter.job_namespace')
                ? $job::class
                : class_basename($job);
        }

        if (config('sql-commenter.driver')) {
            $comment['db_driver'] = $connection->getConfig('driver');
        }

        if (config('sql-commenter.file')) {
            $backtrace = new Backtrace();
            $frame = $backtrace->frames()[8];

            $comment['file'] = $frame->file;
            $comment['line'] = $frame->lineNumber;
        }

        foreach (self::$tags as $key => $value) {
            $comment[$key] = $value;
        }

        $comment = array_filter($comment);

        $query = Str::finish(trim($query), ';');

        if (Str::endsWith($query, ';')) {
            return rtrim($query, ";") . self::formatComments($comment). ';';
        }

        return $query . self::formatComments($comment);
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
}
