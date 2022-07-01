<?php

namespace Spatie\SqlCommenter;

use Illuminate\Database\Connection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SqlCommenterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sql-commenter')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        app('db.connection')->beforeExecuting(function (string &$query, array &$bindings, Connection $connection) {
            $query = SqlCommenter::commentQuery($query, $connection);
        });
    }
}
