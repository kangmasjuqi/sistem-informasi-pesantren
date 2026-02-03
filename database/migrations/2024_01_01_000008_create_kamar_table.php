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
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gedung_id')->constrained('gedung')->onDelete('cascade');
            $table->string('nomor_kamar', 20);
            $table->string('nama_kamar', 100)->nullable();
            $table->integer('lantai')->default(1);
            $table->integer('kapasitas')->comment('Kapasitas maksimal penghuni');
            $table->decimal('luas', 8, 2)->nullable()->comment('Luas dalam m2');
            $table->text('fasilitas')->nullable()->comment('JSON array fasilitas');
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint
            $table->unique(['gedung_id', 'nomor_kamar']);

            // Indexes
            $table->index('gedung_id');
            $table->index('lantai');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};