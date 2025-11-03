<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo')->change();
        });
        
        DB::table('tasks')->where('status', '未着手')->update(['status' => 'todo']);
        DB::table('tasks')->where('status', '進行中')->update(['status' => 'doing']);
        DB::table('tasks')->where('status', '完了')->update(['status' => 'done']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['未着手', '進行中', '完了'])->default('未着手')->change();
        });

         DB::table('tasks')->where('status', 'todo')->update(['status' => '未着手']);
        DB::table('tasks')->where('status', 'doing')->update(['status' => '進行中']);
        DB::table('tasks')->where('status', 'done')->update(['status' => '完了']);
    }
};
