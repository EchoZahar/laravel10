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
        Schema::create('ozon_category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ozon_category_id')->index()->constrained();
            $table->bigInteger('source_id')->comment('id атрибута на мп');
            $table->string('name')->comment('наименование атрибута');
            $table->text('description')->nullable()->comment('описание атрибута');
            $table->string('type')->comment('тип характеристики.');
            $table->boolean('is_collection')->comment('признак, что характеристика — набор значений');
            $table->boolean('is_required')->comment('признак обязательной характеристики');
            $table->integer('group_id')->comment('Идентификатор группы характеристик');
            $table->string('group_name')->nullable()->comment('Название группы характеристик.');
            $table->integer('dictionary_id')->comment('Идентификатор справочника.');
            $table->boolean('is_aspect')->comment('Признак аспектного атрибута. Аспектный атрибут — характеристика, по которой отличаются товары одной модели.');
            $table->boolean('category_dependent')->comment('Признак, что значения словарного атрибута зависят от категории');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_category_attributes');
    }
};
