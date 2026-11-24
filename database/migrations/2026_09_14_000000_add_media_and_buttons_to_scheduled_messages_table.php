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
        Schema::table('scheduled_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_messages', 'media_id')) {
                $table->string('media_id')->nullable();
            }
            if (!Schema::hasColumn('scheduled_messages', 'media_type')) {
                $table->string('media_type')->nullable();
            }
            if (!Schema::hasColumn('scheduled_messages', 'visit_url')) {
                $table->string('visit_url')->nullable();
            }
            if (!Schema::hasColumn('scheduled_messages', 'call_number')) {
                $table->string('call_number')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_messages', function (Blueprint $table) {
            $table->dropColumn(['media_id', 'media_type', 'visit_url', 'call_number']);
        });
    }
};
