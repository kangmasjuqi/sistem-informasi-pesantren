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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_lengkap')->after('name');
            $table->string('username', 50)->unique()->after('email');
            $table->string('telepon', 20)->nullable()->after('password');
            $table->text('alamat')->nullable()->after('telepon');
            $table->string('foto')->nullable()->after('alamat');
            $table->enum('status', ['aktif', 'tidak_aktif', 'banned'])->default('aktif')->after('foto');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->softDeletes();

            // Indexes
            $table->index('username');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'nama_lengkap',
                'username',
                'telepon',
                'alamat',
                'foto',
                'status',
                'last_login_at'
            ]);
        });
    }
};