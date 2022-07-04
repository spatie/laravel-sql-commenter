<?php

namespace Spatie\SqlCommenter\Tests\TestSupport\TestClasses;

use Illuminate\Foundation\Auth\User as BaseUser;

class User extends BaseUser
{
    public $guarded = [];
}
