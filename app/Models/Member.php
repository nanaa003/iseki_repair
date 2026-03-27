<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $connection = 'rifa';
    protected $table = 'employees';
    protected $primaryKey = 'id';

    // Disable timestamps if rifa employees table doesn't have them, or leave as default if it does
    // public $timestamps = false;

    protected $fillable = [
        'nama',
        'nik',
        'team',
        'division_id',
        'status'
    ];
}
