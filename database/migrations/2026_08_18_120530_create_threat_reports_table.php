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
        Schema::create('threat_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('threat_categories')->onDelete('cascade');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->enum('verdict', ['pending', 'confirmed_threat', 'false_positive', 'escalated_externally'])->default('pending');
            $table->text('escalation_note')->nullable();
            $table->enum('status', ['pending', 'under_review', 'in_progress', 'resolved'])->default('pending');
            $table->text('scan_result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threat_reports');
    }
};
