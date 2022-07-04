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
