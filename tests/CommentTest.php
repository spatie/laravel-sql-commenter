<?php

use Spatie\SqlCommenter\Comment;

it('formats comments with keys', function () {
    $comments = collect([
        Comment::make('key1', 'value1'),
        Comment::make('key2', 'value2'),
    ]);

    expect(Comment::formatCollection($comments))->toBe("/*key1='value1',key2='value2'*/");
});

it('formats comments with special characters', function () {
    $comments = collect([
        Comment::make('key1', 'value1@'),
        Comment::make('key2', 'value2'),
    ]);

    expect(Comment::formatCollection($comments))->toBe("/*key1='value1%40',key2='value2'*/");
});
