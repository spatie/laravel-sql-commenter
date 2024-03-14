<?php

namespace Spatie\SqlCommenter;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\SqlCommenter\Commenters\Commenter;
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
        $this->app->scoped(SqlCommenter::class, function () {
            $commenterClass = config('sql-commenter.commenter_class');

            if (! is_a($commenterClass, SqlCommenter::class, true)) {
                throw InvalidSqlCommenter::make($commenterClass);
            }

            return new $commenterClass();
        });


        $connections = config('sql-commenter.connections', []);

        if (empty($connections)) {
            $connections = [config('database.default')];
        }

        collect($connections)->each(fn (string $conn) => DB::connection($conn)->beforeExecuting(function (
            string &$query,
            array &$bindings,
            Connection $connection,
        ) {
            $sqlCommenter = app(SqlCommenter::class);

            $commenters = $this->instanciateCommenters(config('sql-commenter.commenters'));

            $query = $sqlCommenter->commentQuery($query, $connection, $commenters);
        }));
    }

    /**
     * @param array<class-string<Commenter>> $commenterClasses
     *
     * @return array<Commenter>
     */
    protected function instanciateCommenters(array $commenterClasses): array
    {
        return collect($commenterClasses)
            ->mapWithKeys(function (array|string $options, string $commenterClass) {
                if (! is_array($options)) {
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
