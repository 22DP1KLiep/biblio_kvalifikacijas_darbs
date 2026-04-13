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
       Schema::create('conversations', function (Blueprint $table) {
    $table->id();

    // private chat or telegram-style channel
    $table->enum('type', ['private', 'channel']);

    // channel name (null for private chats)
    $table->string('title')->nullable();

    // who owns the channel (null for private chats)
    $table->foreignId('owner_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    // how users can join a channel
    // open = free join
    // request = admin approval required
    $table->enum('join_type', ['open', 'request'])
        ->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
