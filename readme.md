# Backup

This Laravel package allows you to create a backup of your database. You can use any file system which Laravel supports like S3, FTP, local etc.

## Installation
To install the package, run the following command
```
composer require amitavroy/backup-manager
```

Add the service provider inside app.php
```
\Backup\BackupServiceProvider::class,
```

After adding the service provider, publish the config file using
```
php artisan vendor:publish --provider="Backup\BackupServiceProvider"
```

## Configuration
The config file contains documentation on folder structure and other details. If you are using any other file system like S3, you will need to ensure the env variables are setup for the backup to work.

### Some important env variables explained:
| Variable name     | Description           | Default  |
| ------------- |-------------| -----|
| BACKUP_FOLDER_NAME | This is the folder name where the backups will be stored. | DB_DATABASE env |
| BACKUP_DB_FILENAME | This is the file name used along with time at the end | DB_DATABASE env |
| BACKUP_STORAGE_DRIVER | This is the Storage disk which will be used to upload the file. | local |



If you are using S3 file system you will need to run the below command to pull the package

```
composer require league/flysystem-aws-s3-v3
```

For this package to automatically take backup of the database, you need to add the command in you Kernel.php inside the app\Console folder.

Example:

```
protected $commands = [
    BackupDatbase::class,
];

$schedule->command('backup:datatabase')->daily();
```
