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
        Schema::create('pengampu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajar_id')->constrained('pengajar')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'diganti'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint - 1 pengajar untuk 1 mapel di 1 kelas di 1 semester
            // Mencegah double assignment
            $table->unique([
                'pengajar_id',
                'mata_pelajaran_id',
                'kelas_id',
                'semester_id'
            ], 'unique_pengampu_per_semester');

            // Indexes untuk query cepat
            $table->index('pengajar_id');
            $table->index('mata_pelajaran_id');
            $table->index('kelas_id');
            $table->index('semester_id');
            $table->index('status');
            
            // Composite indexes untuk use cases spesifik
            $table->index(['pengajar_id', 'semester_id']); // Query: Pengajar X mengajar apa di semester Y
            $table->index(['kelas_id', 'semester_id']); // Query: Kelas X diajar siapa saja di semester Y
            $table->index(['mata_pelajaran_id', 'semester_id']); // Query: Mapel X diajar oleh siapa di semester Y
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengampu');
    }
};