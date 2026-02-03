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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->foreignId('wali_kelas_id')->nullable()->constrained('pengajar')->onDelete('set null');
            $table->string('nama_kelas', 100)->comment('Contoh: 1A, 2B, Tahfidz 1');
            $table->string('tingkat', 20)->comment('Contoh: 1, 2, 3, Ibtidaiyah, Tsanawiyah');
            $table->integer('kapasitas')->default(30);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tahun_ajaran_id');
            $table->index('wali_kelas_id');
            $table->index('tingkat');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};