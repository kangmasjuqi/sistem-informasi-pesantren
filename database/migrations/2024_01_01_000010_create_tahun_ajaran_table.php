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
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique()->comment('Contoh: 2024/2025');
            $table->year('tahun_mulai');
            $table->year('tahun_selesai');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false)->comment('Hanya 1 tahun ajaran yang aktif');
            $table->text('keterangan')->nullable()->comment('Catatan: Setiap tahun ajaran memiliki 2 semester di tabel semester');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tahun_mulai');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};