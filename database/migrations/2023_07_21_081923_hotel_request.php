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
        Schema::create('hotel_requests', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->integer('user_id');
            $table->json('dependents')->nullable();
            $table->json('room');
            $table->enum('status',['waiting','rejected','confirmed'])->default('waiting');
            $table->timestamps();
            $table->foreign('hotel_id')
              ->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('user_id')
              ->references('performance_num')->on('users')->onDelete('cascade');
            $table->unique(['hotel_id','user_id','created_at']);

        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};