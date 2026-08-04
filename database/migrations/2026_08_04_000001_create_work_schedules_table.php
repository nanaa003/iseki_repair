<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel work_schedules menyimpan pengaturan jam kerja manual (override).
     * Jika suatu tanggal memiliki entry di tabel ini, maka jam kerja yang dipakai
     * untuk perhitungan MTTR adalah jam yang diatur di sini, bukan default.
     */
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->boolean('is_libur')->default(false);
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
