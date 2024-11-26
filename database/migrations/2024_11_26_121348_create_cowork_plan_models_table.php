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
        Schema::create('cowork_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coworking_id')->constrained();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->decimal('price', 20, 2);
            $table->text('benefit');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cowork_plans');
    }
};
