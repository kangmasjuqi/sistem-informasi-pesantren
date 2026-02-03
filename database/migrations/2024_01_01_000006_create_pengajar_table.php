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
        Schema::create('pengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nip', 20)->unique()->comment('Nomor Induk Pengajar');
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('nik', 20)->unique()->nullable();
            $table->text('alamat_lengkap');
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('pendidikan_terakhir', 100)->nullable();
            $table->string('jurusan', 100)->nullable();
            $table->string('universitas', 150)->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->text('keahlian')->nullable()->comment('JSON array keahlian');
            $table->string('foto')->nullable();
            $table->date('tanggal_bergabung');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status_kepegawaian', ['tetap', 'tidak_tetap', 'honorer'])->default('tidak_tetap');
            $table->enum('status', ['aktif', 'non_aktif', 'pensiun'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nip');
            $table->index('jenis_kelamin');
            $table->index('status_kepegawaian');
            $table->index('status');
            $table->index('tanggal_bergabung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajar');
    }
};