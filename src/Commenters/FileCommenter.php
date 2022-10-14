<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\Backtrace\Backtrace;
use Spatie\Backtrace\Frame;
use Spatie\SqlCommenter\Comment;

class FileCommenter implements Commenter
{
    public function __construct(
        public int $backtraceLimit = 40,
        public array $excludePathSegments = [],
    ) {
    }

    /** @return Comment|Comment[]|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        $backtrace = new Backtrace();

        $frames = $backtrace->limit($this->backtraceLimit)->frames();

        $frame = collect($frames)
            ->first(function (Frame $frame) {
                if ($frame->lineNumber === 0) {
                    return false;
                }

                $ignoredPathSegments = array_merge([
                    'laravel-sql-commenter/src',
                    'laravel/framework',
                ], $this->excludePathSegments);

                foreach ($ignoredPathSegments as $ignoredPathSegment) {
                    $segment = str_replace('/', DIRECTORY_SEPARATOR, "/{$ignoredPathSegment}/");

                    if (str_contains($frame->file, $segment)) {
                        return false;
                    }
                }

                return true;
            });

        if (! $frame) {
            return null;
        }

        return [
            Comment::make('file', $frame->file),
            Comment::make('line', $frame->lineNumber),
        ];
    }
}
