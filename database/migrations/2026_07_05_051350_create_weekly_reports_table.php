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
        Schema::dropIfExists('weekly_reports');

        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('generated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('report_number');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('topic')->nullable();
            $table->longText('executive_summary')->nullable();
            $table->longText('plan_of_actions')->nullable();
            $table->string('status')->default('draft');
            $table->string('generated_pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'report_number']);
            $table->index(['project_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};
