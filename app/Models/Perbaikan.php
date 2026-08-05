<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\WorkSchedule;
use App\Models\SpecialDate;

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

    /**
     * Menghitung total menit kerja antara dua waktu, dipotong jam di luar kerja/libur.
     */
    public static function calculateTotalJam($jamStart, $jamFinish)
    {
        $start = Carbon::parse($jamStart);
        $finish = Carbon::parse($jamFinish);
        
        $totalMinutes = 0;
        $current = $start->copy();

        $startDate = $start->format('Y-m-d');
        $endDate = $finish->format('Y-m-d');

        $holidays = SpecialDate::getHolidaysInRange($startDate, $endDate);
        $forcedWorkdays = SpecialDate::getForcedWorkdaysInRange($startDate, $endDate);
        $manualSchedules = WorkSchedule::whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->get()
            ->keyBy(fn($ws) => Carbon::parse($ws->tanggal)->format('Y-m-d'));

        while ($current < $finish) {
            $dateStr = $current->format('Y-m-d');
            $dayOfWeek = $current->dayOfWeek; // 0=Minggu, 6=Sabtu

            // Langkah 1: Cek hari libur dari Rifa
            if (in_array($dateStr, $holidays)) {
                $current->addDay()->setTime(0, 0, 0);
                continue;
            }

            // Langkah 2: Tentukan jam kerja hari ini
            $workStart = null;
            $workEnd = null;

            if (isset($manualSchedules[$dateStr])) {
                $ws = $manualSchedules[$dateStr];
                if ($ws->is_libur) {
                    $current->addDay()->setTime(0, 0, 0);
                    continue;
                }
                $workStart = Carbon::parse($ws->jam_mulai);
                $workEnd = Carbon::parse($ws->jam_selesai);
            } elseif ($dayOfWeek == 0 || $dayOfWeek == 6) {
                // Weekend
                if (in_array($dateStr, $forcedWorkdays)) {
                    $workStart = Carbon::parse('07:30');
                    $workEnd = Carbon::parse('16:30');
                } else {
                    $current->addDay()->setTime(0, 0, 0);
                    continue;
                }
            } else {
                // Weekday
                $workStart = Carbon::parse('07:30');
                if ($dayOfWeek == 5) {
                    // Jumat
                    $workEnd = Carbon::parse('17:00');
                } else {
                    // Senin-Kamis
                    $workEnd = Carbon::parse('16:30');
                }
            }

            // Langkah 3: Hitung waktu kerja
            $dayStart = $current->copy()->setTime(
                $workStart->hour, $workStart->minute, 0
            );
            $dayEnd = $current->copy()->setTime(
                $workEnd->hour, $workEnd->minute, 0
            );

            if ($current < $dayStart) {
                $current = $dayStart->copy();
            }

            if ($current >= $dayEnd) {
                $current->addDay()->setTime(0, 0, 0);
                continue;
            }

            if ($current >= $finish) {
                break;
            }

            if ($current->isSameDay($finish) && $finish < $dayEnd) {
                $totalMinutes += (int) $current->diffInMinutes($finish);
                break;
            } else {
                $totalMinutes += (int) $current->diffInMinutes($dayEnd);
                $current->addDay()->setTime(0, 0, 0);
            }
        }

        return $totalMinutes ?: null;
    }
}
