<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify column type to avoid doctrine/dbal dependency
        DB::statement('ALTER TABLE perbaikans MODIFY Id_Traktor VARCHAR(500)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to integer
        DB::statement('ALTER TABLE perbaikans MODIFY Id_Traktor INT');
    }
};
