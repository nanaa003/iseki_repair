<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkSchedule;
use App\Models\SpecialDate;
use Carbon\Carbon;

class WorkScheduleController extends Controller
{
    /**
     * Halaman kalender pengaturan jam kerja.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $carbonMonth = Carbon::parse($month . '-01');

        $startOfMonth = $carbonMonth->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $carbonMonth->copy()->endOfMonth()->format('Y-m-d');

        // Ambil semua manual overrides bulan ini
        $schedules = WorkSchedule::whereDate('tanggal', '>=', $startOfMonth)
            ->whereDate('tanggal', '<=', $endOfMonth)
            ->get()
            ->keyBy(fn($ws) => Carbon::parse($ws->tanggal)->format('Y-m-d'));

        // Ambil data special dates dari Rifa bulan ini
        $specialDates = [];
        try {
            $specialDates = SpecialDate::getAllInRange($startOfMonth, $endOfMonth)
                ->keyBy(fn($sd) => Carbon::parse($sd->tanggal)->format('Y-m-d'));
        } catch (\Exception $e) {
            // Jika koneksi Rifa gagal, tetap lanjut tanpa data special dates
        }

        // Default jam kerja per hari
        $defaults = [
            1 => ['mulai' => '07:30', 'selesai' => '16:30', 'label' => 'Senin'],
            2 => ['mulai' => '07:30', 'selesai' => '16:30', 'label' => 'Selasa'],
            3 => ['mulai' => '07:30', 'selesai' => '16:30', 'label' => 'Rabu'],
            4 => ['mulai' => '07:30', 'selesai' => '16:30', 'label' => 'Kamis'],
            5 => ['mulai' => '07:30', 'selesai' => '17:00', 'label' => 'Jumat'],
            6 => ['mulai' => '-',     'selesai' => '-',     'label' => 'Sabtu'],
            0 => ['mulai' => '-',     'selesai' => '-',     'label' => 'Minggu'],
        ];

        // Bangun data kalender
        $calendarDays = [];
        $cursor = $carbonMonth->copy()->startOfMonth();
        while ($cursor->month === $carbonMonth->month) {
            $dateStr = $cursor->format('Y-m-d');
            $dow = $cursor->dayOfWeek;
            $default = $defaults[$dow];

            $day = [
                'date'         => $dateStr,
                'day'          => $cursor->day,
                'day_name'     => $default['label'],
                'dow'          => $dow,
                'is_weekend'   => ($dow == 0 || $dow == 6),
                'default_mulai'  => $default['mulai'],
                'default_selesai' => $default['selesai'],
                'override'     => null,
                'special_date' => null,
            ];

            // Cek apakah ada special date dari Rifa
            if (isset($specialDates[$dateStr])) {
                $sd = $specialDates[$dateStr];
                $day['special_date'] = [
                    'jenis' => $sd->jenis_tanggal,
                ];

                // Sesuaikan default tampilan jam jika ada special date Rifa
                if (in_array($sd->jenis_tanggal, ['libur nasional', 'cuti perusahaan', 'libur pengganti'])) {
                    $day['default_mulai'] = '-';
                    $day['default_selesai'] = '-';
                } elseif ($sd->jenis_tanggal === 'libur masuk') {
                    $day['default_mulai'] = '07:30';
                    $day['default_selesai'] = ($dow == 5) ? '17:00' : '16:30';
                }
            }

            // Cek apakah ada override manual
            if (isset($schedules[$dateStr])) {
                $ws = $schedules[$dateStr];
                $day['override'] = [
                    'id'          => $ws->id,
                    'is_libur'    => $ws->is_libur,
                    'jam_mulai'   => $ws->jam_mulai ? Carbon::parse($ws->jam_mulai)->format('H:i') : null,
                    'jam_selesai' => $ws->jam_selesai ? Carbon::parse($ws->jam_selesai)->format('H:i') : null,
                    'keterangan'  => $ws->keterangan,
                ];
            }

            $calendarDays[] = $day;
            $cursor->addDay();
        }

        return view('admin.work-schedules', compact('calendarDays', 'month', 'defaults'));
    }

    /**
     * Simpan atau update pengaturan jam kerja manual untuk tanggal tertentu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'is_libur'    => 'boolean',
            'jam_mulai'   => 'nullable|required_if:is_libur,false|date_format:H:i',
            'jam_selesai' => 'nullable|required_if:is_libur,false|date_format:H:i|after:jam_mulai',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $isLibur = $request->boolean('is_libur');

        $schedule = WorkSchedule::updateOrCreate(
            ['tanggal' => $request->tanggal],
            [
                'is_libur'    => $isLibur,
                'jam_mulai'   => $isLibur ? null : $request->jam_mulai,
                'jam_selesai' => $isLibur ? null : $request->jam_selesai,
                'keterangan'  => $request->keterangan,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jam kerja untuk ' . Carbon::parse($request->tanggal)->format('d M Y') . ' berhasil disimpan.',
            'data'    => $schedule,
        ]);
    }

    /**
     * Hapus pengaturan manual (kembalikan ke default).
     */
    public function destroy($id)
    {
        $schedule = WorkSchedule::findOrFail($id);
        $tanggal = Carbon::parse($schedule->tanggal)->format('d M Y');
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan jam untuk ' . $tanggal . ' dihapus. Kembali ke default.',
        ]);
    }
}
