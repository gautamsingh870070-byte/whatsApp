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
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'notes')) {
                $table->text('notes')->nullable()->after('email');
            }
            if (!Schema::hasColumn('contacts', 'is_manually_saved')) {
                $table->boolean('is_manually_saved')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('contacts', 'is_manually_saved')) {
                $table->dropColumn('is_manually_saved');
            }
        });
    }
};
