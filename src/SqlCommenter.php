<?php

namespace Spatie\SqlCommenter;

use Closure;
use Illuminate\Bus\Dispatcher;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use Laravel\SerializableClosure\Support\ReflectionClosure;

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

        if (config('sqlcommenter.framework')) {
            $comment['framework'] = "laravel-" . app()->version();
        }

        if (request()->route() && config('sqlcommenter.controller')) {
            $action = request()->route()->getAction('uses');

            if ($action instanceof Closure) {
                $reflection = new ReflectionClosure($action);
                $comment['controller'] = 'Closure';
                $comment['action'] = $reflection->getFileName();
            } else {
                $comment['controller'] = config('sqlcommenter.controller_namespace')
                    ? explode('@', $action)[0] ?? null
                    : class_basename(explode('@', $action)[0]);

                $comment['action'] = explode('@', $action)[1] ?? null;
            }
        }

        if (config('sqlcommenter.route')) {
            $comment['url'] = request()->getPathInfo();
            $comment['route'] = request()->route()?->getName();
        }

        if (app()->runningInConsole() && config('sqlcommenter.job')) {
            /** @phpstan-ignore-next-line */
            $pipeline = invade(app(Dispatcher::class))->pipeline;
            /** @phpstan-ignore-next-line */
            $job = invade($pipeline)->passable;

            $comment['job'] = config('sqlcommenter.job_namespace')
                ? $job::class
                : class_basename($job);
        }

        if (config('sqlcommenter.driver')) {
            $comment['db_driver'] = $connection->getConfig('driver');
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
        return self::doubleEscapedUrlEncode($key) . "=" . "'" . self::doubleEscapedUrlEncode($value);
    }

    private static function doubleEscapedUrlEncode(string $input): string
    {
        return str_replace("%", "%%", urlencode($input));
    }
}
