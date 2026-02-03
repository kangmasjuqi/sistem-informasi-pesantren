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
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->foreignId('pengampu_id')->constrained('pengampu')->onDelete('cascade')->comment('Reference ke pengampu (pengajar + mapel + kelas + semester)');
            $table->foreignId('komponen_nilai_id')->constrained('komponen_nilai')->onDelete('cascade');
            $table->decimal('nilai', 5, 2)->comment('Nilai 0-100');
            $table->text('catatan')->nullable()->comment('Catatan dari pengajar');
            $table->date('tanggal_input')->comment('Tanggal nilai diinput');
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint - santri tidak boleh punya nilai komponen yang sama 2x untuk pengampu yang sama
            $table->unique([
                'santri_id', 
                'pengampu_id',
                'komponen_nilai_id'
            ], 'unique_nilai_per_komponen');

            // Indexes
            $table->index('santri_id');
            $table->index('pengampu_id');
            $table->index('komponen_nilai_id');
            $table->index('tanggal_input');
            // Composite index untuk query rapor
            $table->index(['santri_id', 'pengampu_id']);
            $table->index(['pengampu_id', 'komponen_nilai_id']); // Query nilai per komponen per pengampu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};