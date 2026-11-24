<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rcs_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('rcs_agent_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('text'); // text, rich_card
            $table->text('content');
            $table->string('media_url')->nullable();
            $table->json('suggestions')->nullable(); // buttons/actions
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rcs_templates');
    }
};
