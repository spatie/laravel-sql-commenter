<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Spatie\SqlCommenter\Commenters\ControllerCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\UsersController;

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

it('can log the fully qualified controller class name', function () {
    $this->addCommenterToConfig(ControllerCommenter::class, ['includeNamespace' => true]);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
        expect($event->sql)
            ->toContainComment('controller', UsersController::class)
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
