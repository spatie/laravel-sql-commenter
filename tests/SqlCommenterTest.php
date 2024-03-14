<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Spatie\SqlCommenter\Commenters\FrameworkVersionCommenter;
use Spatie\SqlCommenter\Exceptions\InvalidSqlCommenter;
use Spatie\SqlCommenter\SqlCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\CustomCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\InvalidCustomCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\User;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\UsersJob;

it('can add extra comments', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('foo', 'bar');
    });

    app(SqlCommenter::class)->addComment('foo', 'bar');

    dispatch(new UsersJob());
});

it('will not add comments if there already are comments', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->not()->toContainComment('foo', 'bar');
    });

    app(SqlCommenter::class)->addComment('foo', 'bar');

    DB::statement(<<<mysql
        select * from users; /*existing='comment'*/
    mysql);
});

it('can use a custom commenter class', function () {
    config()->set('sql-commenter.commenter_class', CustomCommenter::class);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('framework', 'spatie-framework');
    });

    dispatch(new UsersJob());
});

it('will throw an exception when trying to use an invalid commenter class', function () {
    config()->set('sql-commenter.commenter_class', InvalidCustomCommenter::class);

    dispatch(new UsersJob());
})->throws(InvalidSqlCommenter::class);

it('can disable adding comments via the config file', function () {
    config()->set('sql-commenter.enabled', false);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        $version = app()->version();

        expect($event->sql)->not()->toContainComment('framework', "laravel-{$version}");
    });

    User::all();
});

it('can has a method to disable adding comments', function () {
    SqlCommenter::disable();

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        $version = app()->version();

        expect($event->sql)->not()->toContainComment('framework', "laravel-{$version}");
    });

    User::all();
});

it('has a method to enable adding comments', function () {
    config()->set('sql-commenter.enabled', false);
    $this->addCommenterToConfig(FrameworkVersionCommenter::class);

    SqlCommenter::enable();

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        $version = app()->version();

        expect($event->sql)->toContainComment('framework', "laravel-{$version}");
    });

    User::all();
});

it('will not include empty comments', function () {
    app(SqlCommenter::class)->addComment('foo', 'bar');
    app(SqlCommenter::class)->addComment('baz', '');

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('foo', 'bar');
        expect($event->sql)->not->toContainComment('baz', '');
    });

    User::all();
});
