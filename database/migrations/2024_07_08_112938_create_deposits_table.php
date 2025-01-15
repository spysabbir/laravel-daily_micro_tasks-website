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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('method');
            $table->string('number');
            $table->string('transaction_id');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('rejected_reason')->nullable();
            $table->integer('created_by');
            $table->integer('approved_by')->nullable();
            $table->integer('rejected_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
