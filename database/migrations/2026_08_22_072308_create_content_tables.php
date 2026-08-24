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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('kategori');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('e_issn')->nullable();
            $table->string('cover')->nullable();
            $table->string('link_eksternal');
            $table->enum('status', ['aktif', 'arsip'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal_waktu');
            $table->string('lokasi')->nullable();
            $table->string('poster')->nullable();
            $table->string('info_kontak_pendaftaran')->nullable();
            $table->timestamps();
        });

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['tulisan', 'prestasi', 'produk', 'pkm']);
            $table->string('file')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('organization_profile', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_structure', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengurus');
            $table->string('jabatan');
            $table->string('afiliasi')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi');
            $table->dateTime('tanggal_publish');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('organization_structure');
        Schema::dropIfExists('organization_profile');
        Schema::dropIfExists('publications');
        Schema::dropIfExists('events');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('programs');
    }
};
