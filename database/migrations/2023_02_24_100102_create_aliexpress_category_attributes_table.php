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
        Schema::create('aliexpress_category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aliexpress_category_id')->constrained();
            $table->boolean('is_sku');
            $table->string('name');
            $table->integer('source_id');
            $table->boolean('is_required');
            $table->boolean('is_key');
            $table->boolean('is_brand');
            $table->boolean('is_enum_prop');
            $table->boolean('is_multi_select');
            $table->boolean('is_input_prop');
            $table->boolean('has_unit');
            $table->boolean('has_customized_pic');
            $table->boolean('has_customized_name');
            $table->json('units')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aliexpress_category_attributes');
    }
};
