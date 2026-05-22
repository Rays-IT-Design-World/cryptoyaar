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
        Schema::create('watch_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_id');

            $table->string('reason');

            $table->enum('severity', [
                'low',
                'medium',
                'high',
                'severe'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_flags');
    }
};
