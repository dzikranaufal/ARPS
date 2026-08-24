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
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('kategori_id')->nullable()->after('deskripsi')->constrained('categories')->nullOnDelete();
        });

        // Drop old string column after FK is added (MySQL only; SQLite skips in tests)
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('programs', function (Blueprint $table) {
                $table->string('kategori')->after('deskripsi');
            });
        }
        Schema::table('programs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_id');
        });
    }
};
