<?php

namespace Amitav\Backup;

use Amitav\Backup\Command\BackupDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class BackupServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/./../resources/views', 'backup');

        if (! class_exists('CreateBackupTable')) {
            $this->publishes([
                __DIR__.'/../publishable/migrations/create_backup_table.php'
                => database_path('migrations/'.date('Y_m_d_His', time()).'_create_backup_table.php'),
            ], 'migrations');
        }
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
