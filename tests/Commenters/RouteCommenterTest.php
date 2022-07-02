<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

it('logs the route name and url', function () {
    config()->set('sql-commenter.route', true);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContainComment('url', '/users')
            ->toContainComment('route', 'users.index');
    });

    Route::get('/users', fn () => DB::table('users')->get())->name('users.index');

    $this->get('/users');
});
