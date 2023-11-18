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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned();
            $table->date('date');
            $table->boolean('late')->default(false);
            $table->boolean('no_time_in')->default(false);
            $table->boolean('no_time_out')->default(false);
            $table->boolean('half_day_in')->default(false);
            $table->boolean('half_day_out')->default(false);
            $table->timestamps();



            $table->foreign('employee_id')
            ->references('id')
            ->on('employees')
            ->onDelete('cascade'); // Optional: Specify the on delete action (e.g., cascade)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
