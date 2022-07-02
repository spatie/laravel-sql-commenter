<?php

namespace Spatie\SqlCommenter\Tests\TestSupport\TestClasses;

use Illuminate\Support\Facades\DB;

class UsersController
{
    public function __invoke()
    {
        return DB::table('users')->get();
    }

    public function index()
    {
        return DB::table('users')->get();
    }
}
