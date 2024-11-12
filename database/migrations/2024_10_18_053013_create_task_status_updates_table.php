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
        Schema::create('task_status_updates', function (Blueprint $table) {
            $table->id();
            $table->string('task_status');
            $table->foreignId('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->index(['task_id', 'task_status'], 'task_status_index');
            $table->unique(['task_id', 'task_status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_status_updates');
    }
};
