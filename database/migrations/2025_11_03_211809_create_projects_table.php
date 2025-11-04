<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('key', 12)->unique(); // e.g., PRJ
            $t->text('description')->nullable();
            $t->unsignedBigInteger('issue_seq')->default(0); // for PRJ-xxx
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('projects');
    }
};