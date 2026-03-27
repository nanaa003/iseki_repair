<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perbaikans', function (Blueprint $table) {
            $table->string('Nama_PIC', 255)->nullable()->after('Total_Jam');
        });
    }

    public function down(): void
    {
        Schema::table('perbaikans', function (Blueprint $table) {
            $table->dropColumn('Nama_PIC');
        });
    }
};
