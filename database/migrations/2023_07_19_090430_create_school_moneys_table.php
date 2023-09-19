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
        Schema::create('school_moneys', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->integer('performance_num');
            $table->string('child_name');
            $table->date('child_BD');
            $table->boolean('relative_exists');
            $table->integer('performance_num_relative')->nullable();
            $table->string('child_attachements');
            $table->string('status')->default('waiting');
            $table->boolean('within_age');
            $table->timestamps();
            $table->foreign('performance_num')->references('performance_num')->on('users');
            $table->foreign('performance_num_relative')->references('performance_num')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_moneys');
    }
};
