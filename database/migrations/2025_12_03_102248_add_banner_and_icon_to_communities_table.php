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
        Schema::table('communities', function (Blueprint $table) {
            // Thêm cột icon (Avatar của nhóm)
            if (!Schema::hasColumn('communities', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }
            
            // Thêm cột banner (Ảnh bìa ngang)
            // Lưu ý: Trước đó mình có cột 'cover_image', bạn có thể dùng song song hoặc thay thế tùy ý
            if (!Schema::hasColumn('communities', 'banner')) {
                $table->string('banner')->nullable()->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropColumn(['banner', 'icon']);
        });
    }
};