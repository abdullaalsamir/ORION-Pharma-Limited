<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('half_yearly_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('filename');
            $table->text('description')->nullable();
            $table->date('publication_date');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('half_yearly_reports');
    }
};