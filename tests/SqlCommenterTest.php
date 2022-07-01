<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Spatie\SqlCommenter\Commenters\FileCommenter;
use Spatie\SqlCommenter\SqlCommenter;
use Spatie\SqlCommenter\Tests\TestClasses\CustomCommenter;
use Spatie\SqlCommenter\Tests\TestClasses\User;
use Spatie\SqlCommenter\Tests\TestClasses\UsersController;
use Spatie\SqlCommenter\Tests\TestClasses\UsersJob;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

it('formats comments with keys', function () {
    expect(SqlCommenter::formatComments(["key1" => "value1", "key2" => "value2"]))
        ->toBe("/*key1='value1',key2='value2'*/");
});

it('formats comments without keys', function () {
    expect(SqlCommenter::formatComments([]))
        ->toBe("");
});

it('formats comments with special characters', function () {
    expect(SqlCommenter::formatComments(["key1" => "value1@", "key2" => "value2"]))
        ->toBe("/*key1='value1%40',key2='value2'*/");
});

it('logs the framework version if enabled', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        $version = app()->version();

        expect($event->sql)
            ->toContain(SqlCommenter::formatComment('framework', "laravel-{$version}"));
    });

    DB::table('users')->get();
    User::all();
});

it('logs the controller and action with an invokable controller', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContain(SqlCommenter::formatComment('controller', class_basename(UsersController::class)))
            ->toContain(SqlCommenter::formatComment('action', '__invoke'));
    });

    Route::get('/users', UsersController::class);

    $this->get('/users');
});

it('logs the controller and action with a controller method', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContain(SqlCommenter::formatComment('controller', class_basename(UsersController::class)))
            ->toContain(SqlCommenter::formatComment('action', 'index'));
    });

    Route::get('/users', [UsersController::class, 'index']);

    $this->get('/users');
});

it('logs the controller and action with a closure', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContain(SqlCommenter::formatComment('controller', 'Closure'))
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
            ->toContain(SqlCommenter::formatComment('url', '/users'))
            ->toContain(SqlCommenter::formatComment('route', 'users.index'));
    });

    Route::get('/users', fn () => DB::table('users')->get())->name('users.index');

    $this->get('/users');
});

it('logs the job it originated in', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)->toContain(SqlCommenter::formatComment('job', class_basename(UsersJob::class)));
    });

    dispatch(new UsersJob());
});


it('logs the file it originated in', function () {
    config()->set('sql-commenter.commenters', [new FileCommenter()]);


    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContain(SqlCommenter::formatComment('file', __DIR__ . '/TestClasses/UsersJob.php'));
    });

    dispatch(new UsersJob());
});

it('logs the file it originated in with eloquent', function () {
    config()->set('sql-commenter.commenters', [new FileCommenter()]);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContain(SqlCommenter::formatComment('file', __FILE__));
    });

    User::count();
});

it('can add custom tags', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContain(SqlCommenter::formatComment('foo', 'bar'));
    });

    SqlCommenter::addComment('foo', 'bar');

    dispatch(new UsersJob());
});

it('will not add comments if there already are comments', function () {
    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->not()->toContain(SqlCommenter::formatComment('foo', 'bar'));
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
