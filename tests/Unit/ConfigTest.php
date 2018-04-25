<?php

namespace Amitav\Backup\Tests\Unit;

use Amitav\Backup\Models\Backup;
use Amitav\Backup\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConfigTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function assertTrueIsTrue()
    {
        $backup = Backup::create([
            'uri' => '1',
            'file_system' => '2',
            'file_size' => '3',
            'time_taken' => '4',
        ]);

        $this->assertDatabaseHas('backups', $backup->toArray());
    }
}
