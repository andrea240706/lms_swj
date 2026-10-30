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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('day', 15); // 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
            $table->string('time_slot'); // 'Jam 1 - 3 (07:15 - 09:30)'
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room')->nullable(); // 'Bengkel TBSM 1', 'Mini Bank Syariah'
            $table->string('status', 20)->default('Terjadwal');
            $table->timestamps();

            $table->index(['day', 'time_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
