<?php

namespace Spatie\SqlCommenter;

use Illuminate\Database\Connection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\SqlCommenter\Exceptions\InvalidSqlCommenter;

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
        $this->app->singleton(SqlCommenter::class, function () {
            $commenterClass = config('sql-commenter.commenter_class');

            if (!is_a($commenterClass, SqlCommenter::class, true)) {
                throw InvalidSqlCommenter::make($commenterClass);
            }

            return new $commenterClass();
        });

        $this->app->get('db.connection')
            ->beforeExecuting(function (
                string &$query,
                array &$bindings,
                Connection $connection,
            ) {
                $sqlCommenter = app(SqlCommenter::class);

                $commenters = config('sql-commenter.commenters');

                $query = $sqlCommenter->commentQuery($query, $connection, $commenters);
            });
    }
}
