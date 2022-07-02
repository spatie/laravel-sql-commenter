<?php

namespace Spatie\SqlCommenter\Tests\Commenters;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\UsersJob;

it('logs the job it originated in', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('job', class_basename(UsersJob::class));
    });

    dispatch(new UsersJob());
});
