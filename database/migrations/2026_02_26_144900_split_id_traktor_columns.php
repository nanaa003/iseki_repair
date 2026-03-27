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
        Schema::table('perbaikans', function (Blueprint $table) {
            $table->string('No_Instruksi', 255)->nullable()->after('Id_Traktor');
            $table->string('Type_Traktor', 255)->nullable()->after('No_Instruksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikans', function (Blueprint $table) {
            $table->dropColumn(['No_Instruksi', 'Type_Traktor']);
        });
    }
};
