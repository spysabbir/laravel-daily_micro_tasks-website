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
        Schema::create('default_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('referal_registion_bonus_amount', 10, 2)->nullable();
            $table->decimal('referal_earning_bonus_percentage', 10, 2)->nullable();
            $table->string('deposit_bkash_account')->nullable();
            $table->string('deposit_rocket_account')->nullable();
            $table->string('deposit_nagad_account')->nullable();
            $table->decimal('min_deposit_amount', 10, 2)->nullable();
            $table->decimal('max_deposit_amount', 10, 2)->nullable();
            $table->decimal('instant_withdraw_charge', 10, 2)->nullable();
            $table->decimal('withdraw_charge_percentage', 10, 2)->nullable();
            $table->decimal('min_withdraw_amount', 10, 2)->nullable();
            $table->decimal('max_withdraw_amount', 10, 2)->nullable();
            $table->decimal('job_posting_charge_percentage', 10, 2)->nullable();
            $table->decimal('job_posting_additional_screenshot_charge', 10, 2)->nullable();
            $table->decimal('job_posting_boosted_time_charge', 10, 2)->nullable();
            $table->decimal('job_posting_additional_running_day_charge', 10, 2)->nullable();
            $table->decimal('job_posting_min_budget', 10, 2)->nullable();
            $table->decimal('max_job_proof_bonus_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_settings');
    }
};
