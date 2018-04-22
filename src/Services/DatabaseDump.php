<?php

namespace Amitav\Backup\Services;

use Amitav\Backup\Models\Backup;
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
    private $duration;

    public function __construct()
    {
        $this->filename = config('backup.database_file_name') . '_' . time();
        $now = Carbon::now();
        $backupFolder = config('backup.database_folder_name');
        $this->folderName = "{$backupFolder}/{$now->year}/{$now->month}/{$now->day}";
    }

    public function handle()
    {
        $start = microtime(TRUE);
        $this->takeDump();
        $this->compressBackup();
        $this->uploadFile();
        $this->removeBackupFile();
        $end = microtime(TRUE);

        $this->duration = $this->getDuration($start, $end);
        $this->makeEntry();
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

    protected function getFileSize(File $file)
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

    protected function getDuration($start, $end)
    {
        return date("H:i:s",$end - $start);
    }

    protected function makeEntry()
    {
        Backup::create([
            'uri' => $this->folderName . $this->filename . '.tar.gz',
            'file_system' => config('backup.database_storage_driver'),
            'time_taken' => $this->duration,
            'file_size' => $this->fileSize,
        ]);
    }
}
