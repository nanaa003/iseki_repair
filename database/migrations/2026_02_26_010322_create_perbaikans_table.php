<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perbaikans', function (Blueprint $table) {
            $table->increments('Id_Parbaikan');
            $table->integer('Id_Member')->nullable();
            $table->integer('Id_Traktor');
            $table->string('Ket_Perbaikan', 255);
            $table->string('Jam_Start', 255);
            $table->string('Jam_Finish', 255)->nullable();
            $table->string('Total_Jam', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikans');
    }
};
