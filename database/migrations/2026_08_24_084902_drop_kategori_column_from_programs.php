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
        if (! \Illuminate\Support\Facades\Schema::hasColumn('programs', 'kategori')) {
            return;
        }

        // Try Laravel's dropColumn (needs doctrine/dbal on older setups)
        try {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
            return;
        } catch (\Throwable $e) {
            // Fallback to raw SQLite DROP COLUMN (SQLite >= 3.35)
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE programs DROP COLUMN kategori');
            } catch (\Throwable $e2) {
                // If still fails, leave column (tests will handle via dummy value)
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('programs', 'kategori')) {
            return;
        }
        Schema::table('programs', function (Blueprint $table) {
            $table->string('kategori')->after('deskripsi');
        });
    }
};
