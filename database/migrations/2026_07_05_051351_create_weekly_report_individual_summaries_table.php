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
        Schema::dropIfExists('weekly_report_individual_summaries');

        Schema::create('weekly_report_individual_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('summary_text');
            $table->timestamps();

            $table->unique(['weekly_report_id', 'user_id'], 'wris_weekly_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_report_individual_summaries');
    }
};
