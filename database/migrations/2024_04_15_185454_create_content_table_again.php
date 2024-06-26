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
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Text','Image','Video','Donate'])->default('Text');
            $table->string('title');
            $table->text('description'); // A text column (nullable)
            $table->string('background_image');
            $table->string('content_text');
            $table->string('media_link');
            $table->string('content_category');
            $table->string('lang');
            $table->boolean('is_original');
            $table->boolean('auto_translate');
            $table->boolean('is_draft');
            $table->string('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_table_again');
    }
};
