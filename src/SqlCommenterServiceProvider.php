<?php

namespace Spatie\SqlCommenter;

use Illuminate\Database\Connection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\SqlCommenter\Exceptions\InvalidSqlCommenter;
use Spatie\SqlCommenter\Commenters\Commenter;

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
                string     &$query,
                array      &$bindings,
                Connection $connection,
            ) {
                $sqlCommenter = app(SqlCommenter::class);

                $commenters = $this->instanciateCommenters(config('sql-commenter.commenters'));

                $query = $sqlCommenter->commentQuery($query, $connection, $commenters);
            });
    }

    /**
     * @param array<class-string<Commenter> $commenterClasses
     *
     * @return array<Commenter>
     */
    protected function instanciateCommenters(array $commenterClasses): array
    {
        return collect($commenterClasses)
            ->mapWithKeys(function (array|string $options, string $commenterClass) {
                if (!is_array($options)) {
                    $commenterClass = $options;

                    $options = [];
                }

                return [$commenterClass => $options];
            })
            ->map(function (array $options, string $commenterClass) {
                return app($commenterClass, $options);
            })
            ->toArray();
    }
}
