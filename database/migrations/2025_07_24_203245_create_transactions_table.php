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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->references('id')->on('users');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'declined', 'approved'])->default('pending');
            $table->foreignId('plan_id')->references('id')->on('plans');
            $table->longText('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
