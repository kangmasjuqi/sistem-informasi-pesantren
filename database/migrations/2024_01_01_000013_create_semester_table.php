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
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->enum('jenis_semester', ['ganjil', 'genap']);
            $table->string('nama', 100)->comment('Contoh: Semester Ganjil 2024/2025');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false)->comment('Hanya 1 semester yang aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint - tidak boleh ada 2 semester ganjil/genap di tahun ajaran yang sama
            $table->unique(['tahun_ajaran_id', 'jenis_semester']);

            // Indexes
            $table->index('tahun_ajaran_id');
            $table->index('jenis_semester');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};