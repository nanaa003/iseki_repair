<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perbaikan;
use App\Models\Plan;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RepairController extends Controller
{
    public function verifyTractor(Request $request)
    {
        try {
            $code = $request->input('qr_data');

            if (!$code) {
                return response()->json(['success' => false, 'message' => 'QR Data kosong']);
            }

            $parts = explode(';', $code);
            if (count($parts) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format QR tidak sesuai',
                ]);
            }

            $originalSequenceNo = trim($parts[0]);
            $productionDate = trim($parts[1]);

            $searchSequenceNo = $originalSequenceNo;
            if (!preg_match('/[T]/i', $originalSequenceNo)) {
                $searchSequenceNo = str_pad($originalSequenceNo, 5, '0', STR_PAD_LEFT);
            }

            $plan = Plan::where('Sequence_No_Plan', $searchSequenceNo)
                ->where('Production_Date_Plan', $productionDate)
                ->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data traktor tidak ditemukan di database',
                ]);
            }

            $tractorType = $plan->Model_Name_Plan ?: ($plan->Model_Mower_Plan ?: ($plan->Model_Collector_Plan ?: '-'));

            return response()->json([
                'success' => true,
                'no_instruksi' => $originalSequenceNo,
                'type_traktor' => $tractorType,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Scan Verify Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ]);
        }
    }

    public function verifyNik(Request $request)
    {
        try {
            $nik = $request->input('nik');

            if (!$nik) {
                return response()->json(['success' => false, 'message' => 'NIK kosong']);
            }

            $member = Member::where('nik', $nik)->first();

            if ($member) {
                return response()->json([
                    'success' => true,
                    'nama' => $member->nama,
                    'nik' => $member->nik,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ]);
        }
    }

    public function index(Request $request)
    {
        // Get repairs that are NOT finished yet
        $ongoing = Perbaikan::whereNull('Id_Member')->orderBy('Jam_Start', 'desc')->get();

        // Get recently finished — default to today's date
        $recentQuery = Perbaikan::whereNotNull('Id_Member');

        $filterDate = $request->has('date') && $request->date
            ? $request->date
            : Carbon::today()->format('Y-m-d');

        $recentQuery->whereDate('Jam_Finish', $filterDate);

        $recent = $recentQuery->orderBy('Jam_Finish', 'desc')->get();

        return view('repair.index', compact('ongoing', 'recent'));
    }

    public function create()
    {
        return view('repair.input');
    }

    public function store(Request $request)
    {
        $request->validate([
            'No_Instruksi'      => 'required',
            'Type_Traktor'      => 'required',
            'Ket_Perbaikan'     => 'required',
            'Jam_Start'         => 'required|date_format:Y-m-d H:i:s',
            'Kategori_Perbaikan' => 'required',
            'photo'             => 'nullable|max:512000', // 500 MB (jika file, note: base64 bypasses this)
        ]);

        $photoPath = null;
        if ($request->has('photoData') && !empty($request->photoData)) {
            // Jika dikirim sebagai base64 hasil editan TUI
            $imageParts = explode(";base64,", $request->photoData);
            if (count($imageParts) == 2) {
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1];
                $imageBase64 = base64_decode($imageParts[1]);
                $fileName = 'perbaikan_photo_' . uniqid() . '.' . $imageType;
                \Illuminate\Support\Facades\Storage::disk('public')->put('perbaikan-photos/' . $fileName, $imageBase64);
                $photoPath = 'perbaikan-photos/' . $fileName;
            }
        } elseif ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('perbaikan-photos', 'public');
        }

        Perbaikan::create([
            'No_Instruksi'       => $request->No_Instruksi,
            'Type_Traktor'       => $request->Type_Traktor,
            'Ket_Perbaikan'      => $request->Ket_Perbaikan,
            'Jam_Start'          => $request->Jam_Start,
            'Kategori_Perbaikan' => $request->Kategori_Perbaikan,
            'Photo_Path_Perbaikan' => $photoPath,
        ]);

        return response()->json(['success' => true, 'message' => 'Data saved successfully.']);
    }

    public function finishForm($id)
    {
        $perbaikan = Perbaikan::findOrFail($id);
        return view('repair.finish', compact('perbaikan'));
    }

    public function updateFinish(Request $request, $id)
    {
        $request->validate([
            'Id_Member' => 'required',
            'Jam_Finish' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $perbaikan = Perbaikan::findOrFail($id);

        $start = Carbon::parse($perbaikan->Jam_Start);
        $finish = Carbon::parse($request->Jam_Finish);
        
        $totalMinutes = 0;
        $current = $start->copy();

        while ($current < $finish) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay()->setTime(8, 0, 0);
                continue;
            }

            // Adjust start time to 08:00 if before working hours
            if ($current->format('H:i:s') < '08:00:00') {
                $current->setTime(8, 0, 0);
            }

            // If current time is after or at 17:00, move to next day 08:00
            if ($current->format('H:i:s') >= '17:00:00') {
                $current->addDay()->setTime(8, 0, 0);
                continue;
            }
            
            // If the current has been pushed past finish due to adjustments
            if ($current >= $finish) {
                break;
            }

            // Determine the end time for the current day calculation
            $endOfDay = $current->copy()->setTime(17, 0, 0);
            
            // If the finish time is on the same day and before 17:00
            if ($current->isSameDay($finish) && $finish < $endOfDay) {
                $totalMinutes += (int) $current->diffInMinutes($finish);
                break;
            } else {
                $totalMinutes += (int) $current->diffInMinutes($endOfDay);
                $current->addDay()->setTime(8, 0, 0);
            }
        }

        $member = Member::where('nik', $request->Id_Member)->first();
        $namaPic = $member ? $member->nama : ($request->Nama_PIC ?: '-');

        $perbaikan->update([
            'Id_Member'  => $request->Id_Member,
            'Nama_PIC'   => $namaPic,
            'Jam_Finish' => $request->Jam_Finish,
            'Total_Jam'  => $totalMinutes ?: null,
        ]);

        return response()->json(['success' => true, 'message' => 'Repair finished successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Kategori_Perbaikan' => 'required',
            'Ket_Perbaikan'      => 'required',
        ]);

        $perbaikan = Perbaikan::findOrFail($id);
        
        $perbaikan->update([
            'Kategori_Perbaikan' => $request->Kategori_Perbaikan,
            'Ket_Perbaikan'      => $request->Ket_Perbaikan,
        ]);

        return redirect()->back()->with('success', 'Data perbaikan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $perbaikan = Perbaikan::findOrFail($id);

        // Hapus foto jika ada
        if ($perbaikan->Photo_Path_Perbaikan) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($perbaikan->Photo_Path_Perbaikan);
        }

        $perbaikan->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }
}
