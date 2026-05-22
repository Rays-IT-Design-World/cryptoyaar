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
        Schema::create('subscription_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->decimal('subscription_amount', 10, 2);
            $table->decimal('company_revenue', 10, 2);
            $table->decimal('gst', 10, 2);
            $table->decimal('creator_pool', 10, 2);
            $table->decimal('referral_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_revenues');
    }
};
