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
        Schema::dropIfExists('feature_checklists');

        Schema::create('feature_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_platform_id')->constrained()->cascadeOnDelete();
            $table->string('feature_name');
            $table->string('status')->default('not_started');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['module_platform_id', 'feature_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_checklists');
    }
};
