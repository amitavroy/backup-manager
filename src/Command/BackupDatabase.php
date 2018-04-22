<?php

namespace Amitav\Backup\Command;

use Amitav\Backup\Services\DatabaseDump;
use Amitav\Backup\Services\DiskChecker;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Take a backup of your database.';

    public function handle()
    {
        $disk = config('backup.database_storage_driver');
        $diskChecker = new DiskChecker($disk);
        $diskChecker->handle();

        $dbDump = new DatabaseDump;
        $dbDump->handle();
    }

    protected function checkIfDiskIsPresent()
    {
        $disk = config('backup.database_storage_driver');

        if ($disk == 'local') {
            return true;
        }
    }
}
