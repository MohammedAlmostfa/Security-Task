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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->enum('type', ['Bug', 'Feature', 'Improvement']);
            $table->enum('status', ['Open', 'In Progress', 'Completed', 'Blocked']);
            $table->enum('priority', ['Low', 'Medium', 'High']);
            $table->date('due_date');
            $table->string('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->index('assigned_to');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
