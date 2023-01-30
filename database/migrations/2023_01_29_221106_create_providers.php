<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('providers')->insert([
            'provider' => 'app',
            'unique_key' => 'email',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('providers')->insert([
            'provider' => 'github',
            'unique_key' => 'login',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('providers')->insert([
            'provider' => 'google',
            'unique_key' => 'email',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::raw("delete from providers where provider='app'");
        DB::raw("delete from providers where provider='github'");
        DB::raw("delete from providers where provider='google'");
    }
};
