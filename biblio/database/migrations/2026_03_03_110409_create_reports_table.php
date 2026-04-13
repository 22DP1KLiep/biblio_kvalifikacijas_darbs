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
    Schema::create('reports', function (Blueprint $table) {
        $table->id();

        $table->foreignId('comment_id')
            ->constrained('book_comments')
            ->cascadeOnDelete();

        $table->foreignId('reported_by')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->text('reason')->nullable();

        $table->enum('status', ['pending', 'resolved'])
            ->default('pending');

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('reports');
}

};
