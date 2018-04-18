<?php

namespace Backup\Command;

use Backup\Services\DatabaseDump;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Take a backup of your database.';

    public function handle()
    {
        $dbDump = new DatabaseDump;
        $dbDump->handle();
    }
}
