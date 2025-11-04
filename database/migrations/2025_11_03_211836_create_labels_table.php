<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('labels', function (Blueprint $t) {
            $t->id();
            $t->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('color', 7)->default('#64748b'); // slate-500
            $t->timestamps();
            $t->unique(['organization_id','name']);
        });

        Schema::create('issue_label', function (Blueprint $t) {
            $t->id();
            $t->foreignId('issue_id')->constrained()->cascadeOnDelete();
            $t->foreignId('label_id')->constrained()->cascadeOnDelete();
            $t->unique(['issue_id','label_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('issue_label');
        Schema::dropIfExists('labels');
    }
};