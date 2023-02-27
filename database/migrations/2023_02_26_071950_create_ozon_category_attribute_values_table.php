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
        Schema::create('ozon_category_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->integer('source_attribute_id')->nullable();
            $table->bigInteger('source_id');
            $table->text('value');
            $table->text('info')->nullable();
            $table->string('picture')->nullable();
//            $table->timestamps();
//            $table->foreign('ozon_category_attribute_id', 'ozon_category_attribute_id')->references('id')->on('ozon_category_attributes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_category_attribute_values');
    }
};
