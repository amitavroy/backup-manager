<?php

namespace Amitav\Backup\Tests\Integration;

use Amitav\Backup\Models\Backup;
use Amitav\Backup\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BackupListingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_see_backup_listing()
    {
        $backup = Backup::create([
            'uri' => '1',
            'file_system' => '2',
            'file_size' => '3',
            'time_taken' => '4',
        ]);

        $this->get(route('backup-list'))->assertStatus(200);
    }
}
