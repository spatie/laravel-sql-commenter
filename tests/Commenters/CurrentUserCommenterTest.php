<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Spatie\SqlCommenter\Commenters\CurrentUserCommenter;
use Spatie\SqlCommenter\Tests\TestSupport\TestClasses\User;

it('logs the current user', function () {
    $user = User::create([
        'name' => 'John',
        'password' => 'dummy',
        'email' => 'johndoe@example.com',
    ]);

    auth()->login($user);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) use ($user) {
        expect($event->sql)->toContainComment('user_id', $user->id)
            ->and($event->sql)->toContainComment('user_email', $user->email);
    });

    User::all();
});


it('can exclude the user email', function () {
    $user = User::create([
        'name' => 'John',
        'password' => 'dummy',
        'email' => 'johndoe@example.com',
    ]);

    auth()->login($user);

    $this->addCommenterToConfig(CurrentUserCommenter::class, ['includeEmail' => false]);

    Event::listen(QueryExecuted::class, function (QueryExecuted $event) use ($user) {
        expect($event->sql)->toContainComment('user_id', $user->id)
            ->and($event->sql)->notToContainComment('user_email', $user->email);
    });

    User::all();
});
