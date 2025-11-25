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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Người đăng
            $table->foreignId('community_id')->constrained()->onDelete('cascade'); // Thuộc nhóm nào
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Sapo/Mô tả ngắn
            $table->longText('content'); // Nội dung bài viết (HTML từ TinyMCE)
            $table->string('thumbnail')->nullable(); // Ảnh đại diện bài viết
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
