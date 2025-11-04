<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->foreignId('issue_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('type'); // created_issue, moved_issue, updated_issue, etc.
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['project_id','created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activities');
    }
};