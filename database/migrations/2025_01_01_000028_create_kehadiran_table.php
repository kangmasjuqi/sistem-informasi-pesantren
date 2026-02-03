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
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->foreignId('pengampu_id')->nullable()->constrained('pengampu')->onDelete('set null')->comment('Untuk kehadiran pelajaran, reference ke pengampu');
            $table->foreignId('jadwal_pelajaran_id')->nullable()->constrained('jadwal_pelajaran')->onDelete('set null')->comment('Jadwal spesifik jika ada');
            $table->enum('jenis_kehadiran', ['pelajaran', 'sholat', 'kegiatan'])->default('pelajaran');
            $table->enum('status_kehadiran', ['hadir', 'sakit', 'izin', 'alpa'])->default('hadir');
            $table->time('waktu_absen')->nullable();
            $table->string('keterangan_kegiatan', 100)->nullable()->comment('Untuk jenis kehadiran selain pelajaran');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('santri_id');
            $table->index('pengampu_id');
            $table->index('jadwal_pelajaran_id');
            $table->index('tanggal');
            $table->index('jenis_kehadiran');
            $table->index('status_kehadiran');
            $table->index(['santri_id', 'tanggal']);
            $table->index(['pengampu_id', 'tanggal']); // Query kehadiran per mapel per hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};