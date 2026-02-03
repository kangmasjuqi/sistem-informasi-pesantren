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
        Schema::create('perizinan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_izin', 30)->unique();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->enum('jenis_izin', ['pulang', 'kunjungan', 'sakit', 'keluar_sementara'])->default('pulang');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('waktu_keluar')->nullable();
            $table->time('waktu_kembali')->nullable();
            $table->text('keperluan')->comment('Alasan/keperluan izin');
            $table->string('tujuan', 200)->nullable()->comment('Alamat tujuan');
            $table->string('penjemput_nama', 100)->nullable();
            $table->string('penjemput_hubungan', 50)->nullable()->comment('Hubungan dengan santri');
            $table->string('penjemput_telepon', 20)->nullable();
            $table->string('penjemput_identitas', 50)->nullable()->comment('No KTP/SIM');
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'selesai'])->default('diajukan');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('waktu_persetujuan')->nullable();
            $table->text('catatan_persetujuan')->nullable();
            $table->timestamp('waktu_kembali_aktual')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nomor_izin');
            $table->index('santri_id');
            $table->index('jenis_izin');
            $table->index('status');
            $table->index('tanggal_mulai');
            $table->index(['santri_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan');
    }
};