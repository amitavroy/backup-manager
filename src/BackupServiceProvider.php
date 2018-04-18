<?php

namespace Backup;

use Backup\Command\BackupDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class BackupServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/./../resources/views', 'backup');
    }

    public function register()
    {
        $this->registerPublishables();

        $this->app->bind('command.backup:database', BackupDatabase::class);

        $this->commands(['command.backup:database']);
    }

    protected function registerPublishables()
    {
        $basePath = dirname(__DIR__);

        $arrPublishable = [
            // 'migrations' => [
            //     "$basePath/publishable/database/migrations" => database_path('migrations'),
            // ],
            'config' => [
                "$basePath/publishable/config/backup.php" => config_path('backup.php'),
            ],
        ];

        foreach ($arrPublishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }
}
