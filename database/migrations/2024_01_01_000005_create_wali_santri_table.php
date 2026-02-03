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
        Schema::create('wali_santri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->enum('jenis_wali', ['ayah', 'ibu', 'wali'])->comment('ayah/ibu/wali');
            $table->string('nama_lengkap');
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->decimal('penghasilan', 15, 2)->nullable()->comment('Penghasilan per bulan');
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['hidup', 'meninggal'])->default('hidup');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('santri_id');
            $table->index('jenis_wali');
            $table->index('telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_santri');
    }
};