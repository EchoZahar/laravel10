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
        Schema::create('portal_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('nomenclature_type_id');
            $table->integer('parent_id');
            $table->string('type_name');
            $table->integer('ozon_type_id')->nullable();
            $table->string('ozon_type_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_categories');
    }
};
