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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->string('type'); // FLT, SBY, etc.
        $table->timestamp('check_in')->nullable();
        $table->timestamp('check_out')->nullable();
        $table->string('flight_number')->nullable();
        $table->timestamp('start_time')->nullable();
        $table->timestamp('end_time')->nullable();
        $table->string('start_location')->nullable();
        $table->string('end_location')->nullable();
        $table->timestamps();
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    Schema::dropIfExists('events');
}
};
