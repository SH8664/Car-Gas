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
        Schema::create('cobon_requests', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('cobon_id');
            $table->integer('user_id');
            $table->enum('payment_way',['cash','installments']);
            $table->integer('amount');
            $table->enum('status',['waiting','rejected','confirmed'])->default('waiting');
            $table->boolean('has_partner');
            $table->integer('partner_id')->nullable();
            $table->timestamps();
            $table->foreign('cobon_id')
              ->references('id')->on('cobons');
            $table->foreign('user_id')
              ->references('performance_num')->on('users')->onDelete('cascade');
            $table->unique(['cobon_id','user_id','created_at']);

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