<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doing_lists', function (Blueprint $table) {
            $table->id();
            //$table->foreignId('goal_id')->constrained()->cascadeOnDelete(); //外部キー
            $table->string('task'); // タスク名
            $table->enum('status', ['未着手', '進行中', '完了'])->default('未着手'); //進捗
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doing_lists');
    }
};
