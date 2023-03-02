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
        DB::table('log_levels')->insert([
            'level' => 'FATAL'
        ]);
        DB::table('log_levels')->insert([
            'level' => 'ERROR'
        ]);
        DB::table('log_levels')->insert([
            'level' => 'WARN'
        ]);
        DB::table('log_levels')->insert([
            'level' => 'INFO'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('log_levels')->where('level', '=', 'FATAL')->delete();
        DB::table('log_levels')->where('level', '=', 'ERROR')->delete();
        DB::table('log_levels')->where('level', '=', 'WARN')->delete();
        DB::table('log_levels')->where('level', '=', 'INFO')->delete();
    }
};
