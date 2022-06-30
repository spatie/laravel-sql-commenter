<?php

namespace Spatie\SqlCommenter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\SqlCommenter\Commands\SqlCommenterCommand;

class SqlCommenterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-sqlcommenter')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-sqlcommenter_table')
            ->hasCommand(SqlCommenterCommand::class);
    }
}
