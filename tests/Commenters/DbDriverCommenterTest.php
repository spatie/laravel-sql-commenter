<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Spatie\SqlCommenter\Commenters\DbDriverCommenter;

it('logs the database driver', function () {
    $this->addCommenterToConfig(DbDriverCommenter::class);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('db_driver', 'sqlite');
    });

    DB::table('users')->get();
});
