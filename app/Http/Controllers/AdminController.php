<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Perbaikan;
use App\Models\DuplicateExclusion;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'Username_User' => 'required',
            'Password_User' => 'required'
        ]);

        $user = User::where('Username_User', $request->Username_User)->first();

        if ($user) {
            $isAuthenticated = false;

            try {
                if (\Illuminate\Support\Facades\Hash::check($request->Password_User, $user->Password_User)) {
                    $isAuthenticated = true;
                }
            } catch (\RuntimeException $e) {
                if ($user->Password_User === $request->Password_User) {
                    $isAuthenticated = true;
                    $user->Password_User = \Illuminate\Support\Facades\Hash::make($request->Password_User);
                    $user->save();
                }
            }

            if ($isAuthenticated) {
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->intended('admin/dashboard');
            }
        }

        return back()->withErrors([
            'Username_User' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard(Request $request)
    {
        $kategoriList = [
            'Lecet',
            'Part Kurang Dst',
            'Part Kurang Painting',
            'Part Kurang Assembling',
            'Part NG (di NG kan oleh Produksi)',
            'NG Part (NG Dari Supplier)',
            'Pengencangan',
            'Penyetelan',
            'Perakitan',
            'Susah/Sulit Rakit',
            'Checksheet',
            'Lain-lain'
        ];

        // ── Query utama untuk tabel ───────────────────────────────────────────────
        $query = Perbaikan::query();

        if ($request->filled('date')) {
            $query->whereDate('Jam_Start', $request->date);
        } elseif ($request->filled('month')) {
            $month = Carbon::parse($request->month . '-01');
            $query->whereMonth('Jam_Start', $month->month)->whereYear('Jam_Start', $month->year);
        } else {
            $query->whereDate('Jam_Start', Carbon::today());
        }

        if ($request->filled('kategori')) {
            if ($request->kategori === 'Lain-lain') {
                $query->where('Kategori_Perbaikan', 'LIKE', 'Lain-lain%');
            } else {
                $query->where('Kategori_Perbaikan', $request->kategori);
            }
        }

        $perbaikans = $query->orderBy('Id_Perbaikan', 'desc')->get();

        // ── Query stats (sama filternya) ──────────────────────────────────────────
        $statsQuery = Perbaikan::query();

        if ($request->filled('date')) {
            $statsQuery->whereDate('Jam_Start', $request->date);
            $filterLabel = Carbon::parse($request->date)->format('d M Y');
        } elseif ($request->filled('month')) {
            $month = Carbon::parse($request->month . '-01');
            $statsQuery->whereMonth('Jam_Start', $month->month)->whereYear('Jam_Start', $month->year);
            $filterLabel = $month->format('F Y');
        } else {
            $statsQuery->whereDate('Jam_Start', Carbon::today());
            $filterLabel = 'Hari Ini';
        }

        if ($request->filled('kategori')) {
            if ($request->kategori === 'Lain-lain') {
                $statsQuery->where('Kategori_Perbaikan', 'LIKE', 'Lain-lain%');
            } else {
                $statsQuery->where('Kategori_Perbaikan', $request->kategori);
            }
            $filterLabel .= ' · ' . $request->kategori;
        }

        $totalFiltered    = (clone $statsQuery)->count();
        $finishedFiltered = (clone $statsQuery)->whereNotNull('Jam_Finish')->count();
        $ongoingCount     = Perbaikan::whereNull('Jam_Finish')->count();

        return view('admin.dashboard', compact(
            'perbaikans',
            'totalFiltered',
            'finishedFiltered',
            'ongoingCount',
            'filterLabel',
            'kategoriList'
        ));
    }

    public function exportExcel(Request $request)
    {
        $query = Perbaikan::query();

        $bulanId = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                         'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        if ($request->filled('date')) {
            $tgl = Carbon::parse($request->date);
            $filterLabel = $tgl->day . ' ' . $bulanId[$tgl->month] . ' ' . $tgl->year;
        } elseif ($request->filled('month')) {
            $month = Carbon::parse($request->month . '-01');
            $query->whereMonth('Jam_Start', $month->month)->whereYear('Jam_Start', $month->year);
            $filterLabel = $bulanId[$month->month] . ' ' . $month->year;
        } else {
            $tgl = Carbon::today();
            $query->whereDate('Jam_Start', $tgl);
            $filterLabel = $tgl->day . ' ' . $bulanId[$tgl->month] . ' ' . $tgl->year;
        }

        if ($request->filled('kategori')) {
            if ($request->kategori === 'Lain-lain') {
                $query->where('Kategori_Perbaikan', 'LIKE', 'Lain-lain%');
            } else {
                $query->where('Kategori_Perbaikan', $request->kategori);
            }
        }

        $perbaikans = $query->orderBy('Id_Perbaikan', 'desc')->get();

        // ── Buat Spreadsheet ──────────────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report Perbaikan');

        // Judul
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'LAPORAN PERBAIKAN PERMASALAHAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'Tanggal: ' . $filterLabel);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header kolom (baris 4)
        $headers = ['No', 'No Instruksi', 'Type Traktor', 'Kategori', 'Nama PIC', 'Keterangan', 'Tgl Start', 'Jam Start', 'Tgl Finish', 'Jam Finish', 'Total Jam (Menit)'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

        foreach ($headers as $i => $header) {
            $sheet->setCellValue($cols[$i] . '4', $header);
        }

        $sheet->getStyle('A4:K4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']], // light gray
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $row = 5;
        $totalRows = $perbaikans->count();
        $no = $totalRows;
        $grandTotalMenit = 0;
        foreach ($perbaikans as $p) {
            $tglStart = $p->Jam_Start ? \Carbon\Carbon::parse($p->Jam_Start)->format('d-m-Y') : '-';
            $waktuStart = $p->Jam_Start ? \Carbon\Carbon::parse($p->Jam_Start)->format('H:i:s') : '-';

            $tglFinish = $p->Jam_Finish ? \Carbon\Carbon::parse($p->Jam_Finish)->format('d-m-Y') : '-';
            $waktuFinish = $p->Jam_Finish ? \Carbon\Carbon::parse($p->Jam_Finish)->format('H:i:s') : '-';

            $sheet->setCellValue('A' . $row, $no--);
            $sheet->setCellValueExplicit('B' . $row, $p->No_Instruksi ?? $p->Id_Traktor ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $p->Type_Traktor ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, $p->Kategori_Perbaikan ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row, $p->Nama_PIC ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F' . $row, $p->Ket_Perbaikan ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $row, $tglStart, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $row, $waktuStart, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('I' . $row, $tglFinish, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('J' . $row, $waktuFinish, DataType::TYPE_STRING);
            $menit = $p->Total_Jam ?? '';
            if ($menit !== '') $grandTotalMenit += (int)$menit;
            $sheet->setCellValue('K' . $row, $menit !== '' ? (int)$menit : '');

            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFFF'],
                ],
            ]);

            // Keterangan rata kiri & wrap text
            $sheet->getStyle('F' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Semua kolom lain rata tengah
            $sheet->getStyle('A' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Total baris
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $jam = floor($grandTotalMenit / 60);
        $sisaMenit = $grandTotalMenit % 60;
        $totalLabel = $jam > 0 ? $jam . ' jam ' . $sisaMenit . ' menit' : $sisaMenit . ' menit';
        $sheet->setCellValue('K' . $row, $totalLabel);
        $sheet->getStyle('K' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Auto width kolom
        foreach ($cols as $col) {
            if ($col === 'F') {
                $sheet->getColumnDimension('F')->setWidth(40); // Fix width & allow wrapping
            } else {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $filename = 'Report_Perbaikan_' . str_replace(' ', '_', $filterLabel) . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        $temp_file = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function duplicates(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $carbonMonth = Carbon::parse($month . '-01');
        $m = $carbonMonth->month;
        $y = $carbonMonth->year;

        $perbaikans = Perbaikan::whereNotNull('Ket_Perbaikan')
            ->where('Ket_Perbaikan', '!=', '')
            ->where('Ket_Perbaikan', '!=', '-')
            ->whereMonth('Jam_Start', $m)
            ->whereYear('Jam_Start', $y)
            ->orderBy('Id_Perbaikan')
            ->get();

        $excludedIds = DuplicateExclusion::pluck('perbaikan_id')->toArray();

        $allDuplicates = [];
        $processed     = [];
        $items         = $perbaikans->toArray();

        for ($i = 0; $i < count($items); $i++) {
            if (in_array($i, $processed)) continue;

            $group = [
                'text'          => $items[$i]['Ket_Perbaikan'],
                'texts'         => [$items[$i]['Ket_Perbaikan']],
                'dates'         => [Carbon::parse($items[$i]['Jam_Start'])->format('Y-m-d')],
                'tractors'      => [$items[$i]['No_Instruksi'] ?? $items[$i]['Id_Traktor'] ?? '-'],
                'perbaikan_ids' => [$items[$i]['Id_Perbaikan']],
                'similarities'  => [],
            ];
            $processed[] = $i;
            $baseText    = strtolower(trim($items[$i]['Ket_Perbaikan']));

            for ($j = $i + 1; $j < count($items); $j++) {
                if (in_array($j, $processed)) continue;
                $compareText = strtolower(trim($items[$j]['Ket_Perbaikan']));
                similar_text($baseText, $compareText, $percent);

                if ($percent >= 70) {
                    $group['texts'][]         = $items[$j]['Ket_Perbaikan'];
                    $group['dates'][]         = Carbon::parse($items[$j]['Jam_Start'])->format('Y-m-d');
                    $group['tractors'][]      = $items[$j]['No_Instruksi'] ?? $items[$j]['Id_Traktor'] ?? '-';
                    $group['perbaikan_ids'][] = $items[$j]['Id_Perbaikan'];
                    $group['similarities'][]  = round($percent, 1);
                    $processed[]              = $j;
                }
            }

            if (count($group['texts']) > 1) {
                $activeCount = 0;
                foreach ($group['perbaikan_ids'] as $pid) {
                    if (!in_array($pid, $excludedIds)) $activeCount++;
                }
                $group['count']        = count($group['texts']);
                $group['active_count'] = $activeCount;
                $group['type']         = 'Similar';
                $group['similarity']   = count($group['similarities']) > 0
                    ? round(array_sum($group['similarities']) / count($group['similarities']), 1)
                    : 100;
                $allDuplicates[] = $group;
            }
        }

        usort($allDuplicates, fn($a, $b) => $b['active_count'] <=> $a['active_count']);

        return view('admin.duplicates', compact('allDuplicates', 'month', 'excludedIds'));
    }

    public function excludeDuplicate(Request $request)
    {
        $request->validate(['perbaikan_id' => 'required|integer']);
        DuplicateExclusion::firstOrCreate(['perbaikan_id' => $request->perbaikan_id]);
        return response()->json(['success' => true]);
    }

    public function includeDuplicate(Request $request)
    {
        $request->validate(['perbaikan_id' => 'required|integer']);
        DuplicateExclusion::where('perbaikan_id', $request->perbaikan_id)->delete();
        return response()->json(['success' => true]);
    }
}
