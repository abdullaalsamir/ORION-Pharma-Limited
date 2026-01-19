<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generic_id')->constrained()->onDelete('cascade');
            $table->string('trade_name');
            $table->string('image_path');
            $table->text('preparation')->nullable();
            $table->text('therapeutic_class')->nullable();
            $table->text('indications')->nullable();
            $table->text('dosage_admin')->nullable();
            $table->text('use_children')->nullable();
            $table->text('use_pregnancy_lactation')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('precautions')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('drug_interactions')->nullable();
            $table->text('high_risk')->nullable();
            $table->text('overdosage')->nullable();
            $table->text('storage')->nullable();
            $table->text('presentation')->nullable();
            $table->text('how_supplied')->nullable();
            $table->text('commercial_pack')->nullable();
            $table->text('packaging')->nullable();
            $table->text('official_specification')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
