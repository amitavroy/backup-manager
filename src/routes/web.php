<?php

$namespace = 'Amitav\Backup\Http\Controllers';

Route::group([
    'namespace' => $namespace,
    'prefix' => 'backups',
], function () {
    Route::get('test', 'BackupController@index')->name('backup-list');
});
