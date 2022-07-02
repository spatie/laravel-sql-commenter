<?php

use Spatie\SqlCommenter\Comment;
use Spatie\SqlCommenter\Tests\TestSupport\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toContainComment', function (string $key, ?string $value) {
    expect($this->value)->toContain((string)Comment::make($key, $value));
});
