<?php

namespace Spatie\SqlCommenter;

use Illuminate\Database\Connection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\SqlCommenter\Commenters\Commenter;

class SqlCommenter
{
    /** @var array<string> */
    protected static array $comments = [];

    public static function addComment(string $key, ?string $value): void
    {
        static::$comments[$key] = $value;
    }

    public static function commentQuery(string $query, Connection $connection, array $commenters): string
    {
        if (str_contains($query, '/*')) {
            return $query;
        }

        self::addCommentsFromCommenters($commenters, $connection, $query);

        return self::addCommentsToQuery($query);
    }

    public static function formatComments(array $comments): string
    {
        if (empty($comments)) {
            return '';
        }

        return str(collect($comments)
            ->map(fn ($value, $key) => static::formatComment($key, $value))
            ->join("',"))
            ->prepend('/*')
            ->append("'*/");
    }

    public static function formatComment(string $key, string $value): string
    {
        return urlencode($key) . "=" . "'" . urlencode($value);
    }

    /**
     * @param array<Commenter> $commenters
     * @param \Illuminate\Database\Connection $connection
     * @param string $query
     *
     * @return void
     */
    protected static function addCommentsFromCommenters(
        array $commenters,
        Connection $connection,
        string $query,
    ): void {
        collect($commenters)
            ->flatMap(function (Commenter $commenter) use ($connection, $query) {
                $comments = $commenter->comments($query, $connection) ?? [];

                return Arr::wrap($comments);
            })
            ->filter()
            ->each(fn (Comment $comment) => static::addComment($comment->key, $comment->value));
    }

    protected static function addCommentsToQuery(string $query): string
    {
        $comments = array_filter(self::$comments);

        $query = Str::finish(trim($query), ';');

        if (Str::endsWith($query, ';')) {
            return rtrim($query, ";") . self::formatComments($comments) . ';';
        }

        return $query . static::formatComments($comments);
    }
}
