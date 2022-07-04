<?php

namespace Spatie\SqlCommenter\Tests\Commenters;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Spatie\SqlCommenter\Commenters\FrameworkVersionCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\User;

it('logs the framework version', function () {
    $this->addCommenterToConfig(FrameworkVersionCommenter::class);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        $version = app()->version();

        expect($event->sql)->toContainComment('framework', "laravel-{$version}");
    });

    DB::table('users')->get();
    User::all();
});
