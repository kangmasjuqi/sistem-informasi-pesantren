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
        Schema::create('rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->foreignId('pengampu_id')->constrained('pengampu')->onDelete('cascade')->comment('Reference ke pengampu (pengajar + mapel + kelas + semester)');
            $table->decimal('nilai_akhir', 5, 2)->comment('Nilai akhir hasil perhitungan dari semua komponen');
            $table->char('nilai_huruf', 2)->nullable()->comment('A, B+, B, C+, C, D, E');
            $table->decimal('nilai_angka', 3, 2)->nullable()->comment('Nilai 4.0 scale');
            $table->enum('predikat', ['sangat_baik', 'baik', 'cukup', 'kurang'])->nullable();
            $table->integer('ranking_kelas')->nullable()->comment('Ranking di mata pelajaran ini dalam kelas');
            $table->text('catatan_pengajar')->nullable()->comment('Catatan dari pengajar mata pelajaran');
            $table->text('catatan_wali_kelas')->nullable()->comment('Catatan dari wali kelas');
            $table->boolean('is_lulus')->default(true)->comment('Apakah lulus mata pelajaran ini');
            $table->boolean('is_finalized')->default(false)->comment('Apakah rapor sudah final/tidak bisa diubah');
            $table->foreignId('finalized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint - 1 santri hanya punya 1 rapor per pengampu
            $table->unique([
                'santri_id',
                'pengampu_id'
            ], 'unique_rapor_per_pengampu');

            // Indexes
            $table->index('santri_id');
            $table->index('pengampu_id');
            $table->index('is_finalized');
            $table->index(['santri_id', 'pengampu_id']); // Query rapor santri per pengampu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor');
    }
};