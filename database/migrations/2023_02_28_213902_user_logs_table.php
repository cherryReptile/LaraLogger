<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->string('class');
            $table->json('changed_properties')->nullable();
            $table->json('all_properties')->nullable();
            $table->string('calling_line');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('log_level_id')->constrained('log_levels')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
