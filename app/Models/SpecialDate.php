<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SpecialDate extends Model
{
    /**
     * Konek ke database iseki_rifa untuk membaca data kalender.
     */
    protected $connection = 'rifa';

    protected $table = 'special_dates';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'jenis_tanggal',
    ];

    /**
     * Jenis tanggal yang dianggap sebagai hari libur (hari tidak dihitung).
     */
    const HOLIDAY_TYPES = ['libur nasional', 'cuti perusahaan', 'libur pengganti'];

    /**
     * Jenis tanggal yang memaksa hari tersebut menjadi hari kerja (meskipun weekend).
     */
    const FORCED_WORKDAY_TYPE = 'libur masuk';

    /**
     * Cek apakah suatu tanggal adalah hari libur menurut Rifa.
     */
    public static function isHoliday(string $date): bool
    {
        return self::where('tanggal', $date)
            ->whereIn('jenis_tanggal', self::HOLIDAY_TYPES)
            ->exists();
    }

    /**
     * Cek apakah suatu tanggal adalah "libur masuk" (weekend tapi tetap kerja).
     */
    public static function isForcedWorkday(string $date): bool
    {
        return self::where('tanggal', $date)
            ->where('jenis_tanggal', self::FORCED_WORKDAY_TYPE)
            ->exists();
    }

    /**
     * Ambil semua tanggal libur dalam rentang tertentu.
     */
    public static function getHolidaysInRange(string $startDate, string $endDate): array
    {
        return self::whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->whereIn('jenis_tanggal', self::HOLIDAY_TYPES)
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
    }

    /**
     * Ambil semua tanggal "libur masuk" dalam rentang tertentu.
     */
    public static function getForcedWorkdaysInRange(string $startDate, string $endDate): array
    {
        return self::whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->where('jenis_tanggal', self::FORCED_WORKDAY_TYPE)
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
    }

    /**
     * Ambil semua special dates dalam rentang tertentu untuk tampilan kalender.
     */
    public static function getAllInRange(string $startDate, string $endDate)
    {
        return self::whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->get();
    }
}
