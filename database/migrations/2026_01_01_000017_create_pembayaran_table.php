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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran', 30)->unique()->comment('Nomor transaksi unik');
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
            $table->date('tanggal_pembayaran');
            $table->integer('bulan')->nullable()->comment('Bulan pembayaran untuk tipe bulanan (1-12)');
            $table->year('tahun')->nullable()->comment('Tahun pembayaran untuk tipe bulanan');
            $table->decimal('nominal', 15, 2)->comment('Nominal yang dibayar');
            $table->decimal('potongan', 15, 2)->default(0)->comment('Diskon/potongan');
            $table->decimal('denda', 15, 2)->default(0)->comment('Denda keterlambatan');
            $table->decimal('total_bayar', 15, 2)->comment('Total yang harus dibayar');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris', 'lainnya'])->default('tunai');
            $table->string('nomor_referensi', 100)->nullable()->comment('Nomor referensi transfer/bukti');
            $table->enum('status', ['lunas', 'belum_lunas', 'cicilan'])->default('lunas');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('kode_pembayaran');
            $table->index('santri_id');
            $table->index('jenis_pembayaran_id');
            $table->index('tahun_ajaran_id');
            $table->index('tanggal_pembayaran');
            $table->index('status');
            $table->index(['santri_id', 'bulan', 'tahun']); // Composite index untuk cek pembayaran bulanan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};