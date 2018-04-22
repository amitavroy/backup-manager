<?php

namespace Amitav\Backup\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $table = 'backups';

    protected $guarded = [];
}
