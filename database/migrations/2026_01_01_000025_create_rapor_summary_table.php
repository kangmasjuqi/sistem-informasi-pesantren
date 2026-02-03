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
        Schema::create('rapor_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->decimal('rata_rata', 5, 2)->comment('Rata-rata nilai keseluruhan');
            $table->integer('total_mapel')->comment('Total mata pelajaran yang diikuti');
            $table->integer('total_mapel_lulus')->comment('Total mata pelajaran yang lulus');
            $table->integer('ranking_kelas')->nullable()->comment('Ranking di kelas');
            $table->integer('total_siswa_kelas')->nullable()->comment('Total siswa di kelas');
            $table->integer('total_kehadiran')->default(0)->comment('Total hari masuk');
            $table->integer('total_sakit')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_alpa')->default(0);
            $table->text('catatan_wali_kelas')->nullable();
            $table->text('catatan_kepala_sekolah')->nullable();
            $table->text('saran')->nullable();
            $table->text('prestasi')->nullable()->comment('JSON array prestasi yang diraih');
            $table->text('pelanggaran')->nullable()->comment('JSON array pelanggaran');
            $table->enum('keputusan', ['naik_kelas', 'tinggal_kelas', 'lulus'])->nullable();
            $table->boolean('is_finalized')->default(false);
            $table->foreignId('finalized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint - 1 santri hanya punya 1 summary per semester
            $table->unique(['santri_id', 'semester_id'], 'unique_summary_per_semester');

            // Indexes
            $table->index('santri_id');
            $table->index('kelas_id');
            $table->index('semester_id');
            $table->index('is_finalized');
            $table->index('ranking_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor_summary');
    }
};