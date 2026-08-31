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
        Schema::create('known_threats', function (Blueprint $table) {
            $table->id();
            $table->string('indicator')->unique();
            $table->enum('type', ['file_hash', 'url', 'ip', 'email', 'domain']);
            $table->foreignId('first_reported_report_id')->nullable()->constrained('threat_reports')->onDelete('set null');
            $table->integer('times_reported')->default(1);
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('added_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('known_threats');
    }
};
