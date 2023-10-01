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
            $table->dateTime('date');
            $table->json('remarks')->nullable();
            $table->boolean('is_resolve')->default(false);
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
