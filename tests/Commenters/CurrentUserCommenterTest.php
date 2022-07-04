<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Spatie\SqlCommenter\Commenters\ControllerCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\User;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\UsersController;

it('logs the current user', function () {
    $user = User::create([
        'name' => 'John',
       'password' => 'dummy',
       'email' => 'johndoe@example.com'
    ]);

    auth()->login($user);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) use ($user) {

        expect($event->sql)->toContainComment('user_id', $user->id)
            ->and($event->sql)->toContainComment('user_email', $user->email);
    });

    User::all();
});


