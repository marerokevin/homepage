<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // <--- Make sure this is here
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change 'Table' to 'Blueprint' right here:
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique(); // Don't forget your slug!
            $table->text('body');
            $table->date('event_date')->nullable(); // Add your new date column here too
            $table->text('image')->nullable();
            $table->string('location')->nullable(); // NEW COLUMN
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
