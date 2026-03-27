<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $connection = 'podium';
    protected $table = 'plans';
    protected $primaryKey = 'Id_Plan';

    public $timestamps = false;

    protected $fillable = [
        'Id_Plan',
        'Type_Plan',
        'Sequence_No_Plan',
        'Production_Date_Plan',
        'Model_Name_Plan',
        'Production_No_Plan',
        'Chasis_No_Plan',
        'Model_Label_Plan',
        'Safety_Frame_Label_Plan',
        'Model_Mower_Plan',
        'Mower_No_Plan',
        'Model_Collector_Plan',
        'Collector_No_Plan',
        'Record_Plan',
        'Lineoff_Plan',
        'Status_Plan'
    ];
}
