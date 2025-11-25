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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Polymorphic relations (Dùng chung cho cả Post và Comment sau này)
            $table->unsignedBigInteger('votable_id');
            $table->string('votable_type');
            $table->tinyInteger('value'); // 1 là upvote, -1 là downvote
            $table->timestamps();

            // Một user chỉ được vote 1 lần cho 1 item
            $table->unique(['user_id', 'votable_id', 'votable_type']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
