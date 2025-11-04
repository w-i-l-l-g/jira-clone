<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('issues', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->foreignId('column_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('key')->unique(); // PRJ-1
            $t->string('title');
            $t->text('description')->nullable();
            $t->enum('priority', ['low','medium','high','urgent'])->default('medium');
            $t->integer('estimate')->nullable();
            $t->timestamps();
            $t->index(['project_id','column_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('issues');
    }
};