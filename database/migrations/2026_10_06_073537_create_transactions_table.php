<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('status')->default('Dipinjam');
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->string('from_person')->nullable();
            $table->string('to_person')->nullable();
            $table->integer('quantity')->default(1);
            $table->dateTime('transaction_date');
            $table->string('condition')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
