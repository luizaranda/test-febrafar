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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('due_date');
            $table->dateTime('completion_date')->nullable();
            $table->enum('status', ['open', 'completed'])->default('open');
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
