<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $perbaikans = DB::table('perbaikans')->whereNotNull('Total_Jam')->get();
        foreach ($perbaikans as $p) {
            $total = 0;
            if (preg_match('/(\d+)\s*jam/', $p->Total_Jam, $m)) $total += (int)$m[1] * 60;
            if (preg_match('/(\d+)\s*menit/', $p->Total_Jam, $m)) $total += (int)$m[1];
            DB::table('perbaikans')->where('Id_Perbaikan', $p->Id_Perbaikan)->update(['Total_Jam' => $total ?: null]);
        }

        Schema::table('perbaikans', function (Blueprint $table) {
            $table->integer('Total_Jam')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('perbaikans', function (Blueprint $table) {
            $table->string('Total_Jam', 255)->nullable()->change();
        });
    }
};
