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
        Schema::create('holiday_temps', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('holiday_id')->unsigned();
            $table->date('date');
            $table->timestamps();


            $table->foreign('holiday_id')
            ->references('id')
            ->on('holidays')
            ->onDelete('cascade'); 
           
        });
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_temps');
    }
};
