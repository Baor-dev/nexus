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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Người báo cáo
            
            // Polymorphic: Report cái gì? (User hay Post)
            $table->unsignedBigInteger('reportable_id');
            $table->string('reportable_type');
            
            $table->string('reason'); // Lý do báo cáo
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending'); // Trạng thái xử lý
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
