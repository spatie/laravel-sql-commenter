<?php

namespace Spatie\SqlCommenter\Commenters;

use Illuminate\Database\Connection;
use Spatie\Backtrace\Backtrace;
use Spatie\Backtrace\Frame;
use Spatie\SqlCommenter\Comment;

class FileCommenter implements Commenter
{
    public function __construct(
        public int $backtraceLimit = 70
    ) {
    }

    /** @return Comment|array<Comment>|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        $backtrace = new Backtrace();

        $frames = $backtrace->limit($this->backtraceLimit)->frames();

        $frame = collect($frames)
            ->first(function (Frame $frame) {
                if ($frame->lineNumber === 0) {
                    return false;
                }

                if (str_contains($frame->file, 'laravel/framework')) {
                    return false;
                }

                if (str_contains($frame->file, 'laravel-sql-commenter/src')) {
                    return false;
                }

                return true;
            });

        if (! $frame) {
            return null;
        }

        return [
            new Comment('file', $frame->file),
            new Comment('line', $frame->lineNumber),
        ];
    }
}
