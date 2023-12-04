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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned();
            $table->string('serial_number');
            $table->string('state');
            $table->timestamp('timestamp');
            $table->string('type');
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
        Schema::dropIfExists('attendances');
    }
};
