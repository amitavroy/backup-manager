<?php

namespace Amitav\Backup\Services;

class DiskChecker
{
    protected $disk;

    protected $packages;

    /**
     * DiskChecker constructor.
     * @param $disk
     */
    public function __construct($disk)
    {
        $this->disk = $disk;
    }

    public function handle()
    {
        if ($this->disk == 'local') {
            return true;
        }

        $this->loadPackageInformation();

        if ($this->disk == 's3') {
            $this->checkIfS3SupportIsPresent();
        }
    }

    protected function loadPackageInformation()
    {
        $file = base_path('composer.lock');
        $this->packages = json_decode(file_get_contents($file), true)['packages'];
    }

    protected function checkIfS3SupportIsPresent()
    {
        foreach ($this->packages as $key => $package) {
            if ($package['name'] == 'league/flysystem-aws-s3-v35') {
                return true;
            }
        }

        throw new PackageNotFound('The league/flysystem-aws-s3-v3 package is required for S3 backup.');
    }
}
