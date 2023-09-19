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
        Schema::create('resort_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('performance_num');
            // $table->string('resort_name');
            // $table->date('resort_start_time');
            // $table->date('resort_end_time');
            $table->unsignedBigInteger('first_desire_id')->nullable();
            $table->unsignedBigInteger('second_desire_id')->nullable();
            $table->unsignedBigInteger('third_desire_id')->nullable();
            $table->string('status')->default('waiting');
            $table->json('relatives')->nullable();
            $table->timestamps();
           // $table->unique(['performance_num','first_desire_id','second_desire_id','third_desire_id'],'unique key');
            $table->foreign('performance_num')->references('performance_num')->on('users');
            $table->foreign('first_desire_id')->references('id')->on('resorts')->onDelete('set null');
            $table->foreign('second_desire_id')->references('id')->on('resorts')->onDelete('set null');;
            $table->foreign('third_desire_id')->references('id')->on('resorts')->onDelete('set null');;

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resort_requests');
    }
};
