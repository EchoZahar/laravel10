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
        Schema::create('ali_category_attribute_ali_category_attribute_value', function (Blueprint $table) {
            $table->bigInteger('ali_category_attribute_id');
            $table->bigInteger('ali_category_attribute_value_id');
            $table->primary(['ali_category_attribute_id', 'ali_category_attribute_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ali_category_attribute_ali_category_attribute_value');
    }
};
