<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rcs_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('agent_id')->unique();
            $table->string('brand_id');
            $table->text('service_account_json'); // Store securely
            $table->string('webhook_url')->nullable();
            $table->string('webhook_token')->nullable();
            $table->string('environment')->default('sandbox'); // sandbox, production
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rcs_agents');
    }
};
