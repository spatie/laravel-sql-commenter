<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Spatie\SqlCommenter\Commenters\FileCommenter;
use Spatie\SqlCommenter\SqlCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\CustomCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\User;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\UsersController;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\UsersJob;

it('logs the framework version', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        $version = app()->version();

        expect($event->sql)->toContainComment('framework', "laravel-{$version}");
    });

    DB::table('users')->get();
    User::all();
});

it('logs the controller and action with an invokable controller', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContainComment('controller', class_basename(UsersController::class))
            ->toContainComment('action', '__invoke');
    });

    Route::get('/users', UsersController::class);

    $this->get('/users');
});

it('logs the controller and action with a controller method', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContainComment('controller', class_basename(UsersController::class))
            ->toContainComment('action', 'index');
    });

    Route::get('/users', [UsersController::class, 'index']);

    $this->get('/users');
});

it('logs the controller and action with a closure', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContainComment('controller', 'Closure')
            ->toContain('SqlCommenterTest.php');
    });

    Route::get('/users', function () {
        return DB::table('users')->get();
    });

    $this->get('/users');
});

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

it('logs the job it originated in', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('job', class_basename(UsersJob::class));
    });

    dispatch(new UsersJob());
});


it('logs the file it originated in', function () {
    config()->set('sql-commenter.commenters', [new FileCommenter()]);


    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('file', __DIR__ . '/TestSupport/TestClasses/UsersJob.php');
    });

    dispatch(new UsersJob());
});

it('logs the file it originated in with eloquent', function () {
    config()->set('sql-commenter.commenters', [new FileCommenter()]);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('file', __FILE__);
    });

    User::count();
});

it('can add extra comments', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContainComment('foo', 'bar');
    });

    SqlCommenter::addComment('foo', 'bar');

    dispatch(new UsersJob());
});

it('will not add comments if there already are comments', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->not()->toContainComment('foo', 'bar');
    });

    SqlCommenter::addComment('foo', 'bar');

    DB::statement(<<<mysql
        select * from users; /*existing='comment'*/
    mysql);
});

it('can use a custom commenter class', function () {
    config()->set('sql-commenter.commenter_class', CustomCommenter::class);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContain(CustomCommenter::formatComment('framework', 'spatie-framework'));
    });

    dispatch(new UsersJob());
});
