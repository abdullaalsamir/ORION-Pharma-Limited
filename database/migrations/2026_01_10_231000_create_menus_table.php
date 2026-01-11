<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->longText('content')->nullable();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);

            $table->boolean('is_active')->default(1);
            $table->boolean('is_multifunctional')->default(0);

            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('menus')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
