<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('columns', function (Blueprint $t) {
            $t->id();
            $t->foreignId('board_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->integer('position'); // 100, 200, 300 for easy inserts
            $t->timestamps();
            $t->index(['board_id','position']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('columns');
    }
};