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
        Schema::create('ali_category_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ali_attribute_id')->nullable();
            $table->json('values')->nullable();
            $table->json('error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ali_category_attribute_values');
    }
};
