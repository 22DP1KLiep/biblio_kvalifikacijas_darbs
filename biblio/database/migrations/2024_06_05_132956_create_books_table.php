<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // svarīgi, lai var sasaistīt ar citām tabulām
            $table->string('title');
            $table->string('author');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('published_year');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
