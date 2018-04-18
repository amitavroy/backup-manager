<?php

namespace Amitav\Backup\Services;

use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Databases\MySql;
use Symfony\Component\Process\Process;

class DatabaseDump
{
    private $filename;
    private $fileSize;
    private $folderName;

    public function __construct()
    {
        $this->filename = config('backup.database_file_name') . '_' . time();
        $now = Carbon::now();
        $backupFolder = config('backup.database_folder_name');
        $this->folderName = "{$backupFolder}/{$now->year}/{$now->month}/{$now->day}";
    }

    public function handle()
    {
        $this->takeDump();
        $this->compressBackup();
        $this->uploadFile();
        $this->removeBackupFile();
    }

    protected function takeDump()
    {
        try {
            MySql::create()
                ->setDbName(config('backup.database_name'))
                ->setUserName(config('backup.dabase_username'))
                ->setPassword(config('backup.database_password'))
                ->dumpToFile($this->filename . '.sql');
        } catch (\Exception $exception) {
            \Log::info($exception->getMessage());
        }
    }

    protected function compressBackup()
    {
        try {
            $process = new Process("tar -zcf {$this->filename}.tar.gz {$this->filename}.sql");
            $process->run();
        } catch (\Exception $exception) {
            \Log::info($exception->getMessage());
        }
    }

    protected function removeBackupFile()
    {
        unlink($this->filename . '.sql');
        unlink($this->filename . '.tar.gz');
    }

    protected function uploadFile()
    {
        try {
            $file = new File($this->filename . '.tar.gz');
            $this->fileSize = $this->getFileSize($file);
            Storage::disk(config('backup.database_storage_driver'))->putFileAs($this->folderName, $file, $this->filename . '.tar.gz');
        } catch (\Exception $exception) {
            \Log::info($exception->getMessage());
        }
    }

    private function getFileSize(File $file)
    {
        $fileSize = $file->getSize();
        $unit = 'Bytes';
        if ($fileSize > 1024) {
            $fileSize = $fileSize / 1024;
            $unit = 'KB';
        }
        if ($fileSize > 1024) {
            $fileSize = $fileSize / 1024;
            $unit = 'MB';
        }
        return round($fileSize) . ' ' . $unit;
    }
}
