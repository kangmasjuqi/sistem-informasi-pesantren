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
        Schema::create('gedung', function (Blueprint $table) {
            $table->id();
            $table->string('kode_gedung', 20)->unique();
            $table->string('nama_gedung');
            $table->enum('jenis_gedung', ['asrama_putra', 'asrama_putri', 'kelas', 'serbaguna', 'masjid', 'kantor', 'perpustakaan', 'lab', 'dapur', 'lainnya']);
            $table->integer('jumlah_lantai')->default(1);
            $table->integer('kapasitas_total')->nullable()->comment('Kapasitas total orang');
            $table->text('alamat_lokasi')->nullable();
            $table->year('tahun_dibangun')->nullable();
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->text('fasilitas')->nullable()->comment('JSON array fasilitas');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('kode_gedung');
            $table->index('jenis_gedung');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gedung');
    }
};