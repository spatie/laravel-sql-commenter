<?php

namespace Spatie\SqlCommenter\Tests\TestSupport;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\SqlCommenter\Commenters\JobCommenter;
use Spatie\SqlCommenter\SqlCommenterServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\SqlCommenter\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SqlCommenterServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../../vendor/orchestra/testbench-core/laravel/migrations/2014_10_12_000000_testbench_create_users_table.php';
        $migration->up();
    }

    public function addCommenterToConfig(string $commenterClass, array $options = []): self
    {
        $commenters = config('sql-commenter.commenters');
        $commenters[$commenterClass] = $options;
        config()->set('sql-commenter.commenters', $commenters);

        return $this;

    }
}
