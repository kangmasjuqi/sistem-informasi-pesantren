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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_inventaris_id')->constrained('kategori_inventaris')->onDelete('cascade');
            $table->foreignId('gedung_id')->nullable()->constrained('gedung')->onDelete('set null');
            $table->string('kode_inventaris', 30)->unique();
            $table->string('nama_barang');
            $table->string('merk', 100)->nullable();
            $table->string('tipe_model', 100)->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('satuan', 20)->default('unit')->comment('unit, buah, set, dll');
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->default('baik');
            $table->date('tanggal_perolehan');
            $table->decimal('harga_perolehan', 15, 2)->nullable();
            $table->decimal('nilai_penyusutan', 15, 2)->nullable();
            $table->string('sumber_dana', 100)->nullable()->comment('APBN, Donasi, dll');
            $table->string('lokasi', 200)->nullable()->comment('Lokasi penyimpanan detail');
            $table->text('spesifikasi')->nullable();
            $table->string('nomor_seri', 100)->nullable();
            $table->date('tanggal_maintenance_terakhir')->nullable();
            $table->string('penanggung_jawab', 100)->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('kode_inventaris');
            $table->index('kategori_inventaris_id');
            $table->index('gedung_id');
            $table->index('kondisi');
            $table->index('is_active');
            $table->index('tanggal_perolehan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};