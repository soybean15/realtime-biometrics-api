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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->json('data'); 
            $table->timestamps();
        });

        $defaultSettings = [
            'dark_mode' => true,
    
    
            'theme'=>[
                'primary'=>'#49b265',
                'secondary'=>'#26A69A'
            ],
            'zkteco'=>0,
            'live_update'=>false,
            '24hrs_format'=>false

        ];
    
        DB::table('settings')->insert([
            'data' => json_encode($defaultSettings),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
