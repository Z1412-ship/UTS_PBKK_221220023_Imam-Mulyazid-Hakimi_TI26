<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->ulid('author_id')->primary(); // ULID sebagai primary key
            $table->string('name'); // Kolom name
            $table->string('nationality'); // Kolom nationality
            $table->string('birthdate'); // Kolom birthdate sebagai string
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};