<?php

$namespace = 'Amitav\Backup\Http\Controllers';

Route::group([
    'namespace' => $namespace
], function () {
    Route::get('test', 'BackupController@index')->name('backup-list');
});
