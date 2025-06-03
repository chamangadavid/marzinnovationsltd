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
            $table->string('transactionId')->unique();
            $table->string('transactionName');
            $table->string('clientName');
            $table->string('clientEmail');
            $table->string('clientMobile')->nullable();
            $table->string('clientTpin')->nullable();
            $table->text('clientAddress')->nullable();
            $table->string('status')->default('Pending');
            $table->integer('quantity');
            $table->decimal('unitPrice', 10, 2);
            $table->decimal('totalAmount', 10, 2);
            $table->decimal('initial_pay', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
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
