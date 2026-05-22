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
        Schema::table('video_views', function (Blueprint $table) {
            $table->string('ip_address')->nullable();

            $table->string('device_id')->nullable();

            $table->decimal('completion_rate', 5, 2)->default(0);

            $table->string('traffic_source')->nullable();

            $table->boolean('is_flagged')->default(false);

            $table->integer('fraud_score')->default(0);

            $table->text('fraud_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_views', function (Blueprint $table) {
            //
        });
    }
};
