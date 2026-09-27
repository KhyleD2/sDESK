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
        Schema::table('threat_reports', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at timestamp column
            $table->string('archived_by')->nullable(); // Track who archived it
            $table->string('archive_reason')->nullable(); // Optional reason for archiving
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threat_reports', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['archived_by', 'archive_reason']);
        });
    }
};
