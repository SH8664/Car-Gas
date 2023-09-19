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
        Schema::create('travel_allowances', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');
            $table->string('from');
            $table->string('to');
            $table->string('accommodation_type');
            $table->integer('meals_count');
            $table->integer('meals_cost');
            $table->integer('transport_count');
            $table->integer('transport_cost');
            $table->integer('travel_cost');
            $table->integer('total');
            $table->timestamps();

            $table->foreign('user_id')
            ->references('performance_num')->on('users')->onDelete('cascade');
            $table->unique(['user_id','start_date','end_date']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_allowances');
    }
};
