<?php

namespace Spatie\SqlCommenter\Commands;

use Illuminate\Console\Command;

class SqlCommenterCommand extends Command
{
    public $signature = 'laravel-sqlcommenter';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
