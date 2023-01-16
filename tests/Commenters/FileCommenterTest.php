<?php

namespace Spatie\SqlCommenter\Tests\Commenters;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Spatie\SqlCommenter\Commenters\FileCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\User;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\UsersJob;

it('logs the file it originated in', function () {
    $this->addCommenterToConfig(FileCommenter::class);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('file', realpath(__DIR__ . '/../TestSupport/TestClasses/UsersJob.php'));
    });

    dispatch(new UsersJob());
});

it('logs the file it originated in with eloquent', function () {
    $this->addCommenterToConfig(FileCommenter::class);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('file', __FILE__);
    });

    User::count();
});

it('logs the file use relative path', function () {
    $this->addCommenterToConfig(FileCommenter::class, ['useRelativePath' => true]);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->not()->toContainComment('file', __FILE__);
        expect($event->sql)->toContainComment('file', substr(__FILE__, strlen(__DIR__) + 1));
    });

    app()->setBasePath(__DIR__);
    User::count();
});
