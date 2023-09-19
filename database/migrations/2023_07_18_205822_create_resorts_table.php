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
        Schema::create('resorts', function (Blueprint $table) {
            //primary key is the name
            $table->id()->nullable();
            $table->string('name');
            // the whole duration
            $table->date('start_time');
            $table->date('end_time');
            //for managers
            $table->integer('price1');
            //for the rest of the employees
            $table->integer('price2');
            $table->boolean('is_available');
            $table->timestamps();
            $table->index('start_time');
            $table->index('end_time');
            $table->index('name');
            $table->unique(['name', 'start_time','end_time']);
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resorts');
    }
};
