<?php

namespace App\Models;

use CodeIgniter\Model;

class Project extends Model
{

    protected $table      = 'projects';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'ppk_id',
        'kontraktor_id',
        'konsultan_id',
        'name',
        'location',
        'start_date',
        'end_date',
        'description',
        'status',
        'progress_percentage',
        'created_at',
        'updated_at',
    ];
}