<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perbaikan extends Model
{
    protected $primaryKey = 'Id_Perbaikan';

    protected $fillable = [
        'Id_Member',
        'No_Instruksi',
        'Type_Traktor',
        'Ket_Perbaikan',
        'Jam_Start',
        'Jam_Finish',
        'Total_Jam',
        'Nama_PIC',
        'Photo_Path_Perbaikan',
        'Kategori_Perbaikan',
    ];
}
