<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('notifications', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Kam pieder notifikācija

        $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('cascade');
        // Kas izraisīja notifikāciju

        $table->string('type');
        // follow | message | comment

        $table->json('data')->nullable();
        // Papildus info (piemēram chat_id)

        $table->boolean('is_read')->default(false);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
